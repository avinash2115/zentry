<?php

namespace App\Components\Sessions\Http\Controllers\Stream;

use App\Assistants\Files\Http\Controllers\Traits\FileReceiverTrait;
use App\Assistants\Files\Http\Controllers\Traits\Resumable\PartialTrait;
use App\Assistants\Files\Services\Stream\Video\Stream;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Services\Stream\StreamServiceContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Sessions\ValueObjects\Stream\Token;
use App\Components\Sessions\ValueObjects\Upload\Progress;
use App\Convention\Exceptions\Auth\UnauthorizedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Storage\File\ReadException;
use App\Convention\Exceptions\Storage\File\UploadException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Http\Controllers\Controller as BaseController;
use Cache;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Kladislav\LaravelChunkUpload\Exceptions\UploadMissingFileException;
use Kladislav\LaravelChunkUpload\Receiver\FileReceiver;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as SResponse;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Sessions\Http\Controllers\Stream
 */
class Controller extends BaseController
{
    use SessionServiceTrait;
    use LinkParametersTrait;
    use FileReceiverTrait;
    use PartialTrait;

    /**
     * @param string $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|PropertyNotInit|NotFoundException|UnexpectedValueException
     */
    public function index(string $sessionId): Response
    {
        return $this->sendResponse($this->_streamService($sessionId)->list());
    }

    /**
     * @param string $sessionId
     * @param string $streamId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function token(string $sessionId, string $streamId): Response
    {
        $token = new Token(IdentityGenerator::next()->toString());

        Cache::put(
            $token->token,
            $this->_streamService($sessionId, $streamId)->readonly()->url(),
            StreamReadonlyContract::PLAY_TOKEN_TTL
        );

        return $this->sendResponse($token);
    }

    /**
     * @param Request $request
     * @param string  $sessionId
     * @param string  $streamId
     * @param string  $token
     *
     * @return SResponse
     * @throws BindingResolutionException
     * @throws FileNotFoundException
     * @throws PropertyNotInit
     * @throws ReadException
     * @throws UnauthorizedException
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function play(Request $request, string $sessionId, string $streamId, string $token): SResponse
    {
        $url = Cache::get($token);

        if (!is_string($url)) {
            throw new UnauthorizedException();
        }

        Cache::put($token, $url, StreamReadonlyContract::PLAY_TOKEN_TTL);

        return (new Stream())->play($url);
    }

    /**
     * @param string $sessionId
     * @param string $streamId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|RuntimeException|NotFoundException
     */
    public function temporaryUrl(string $sessionId, string $streamId): Response
    {
        return $this->sendResponse($this->_streamService($sessionId, $streamId)->temporaryUrl());
    }

    /**
     * @param string      $sessionId
     * @param string|null $streamId
     *
     * @return StreamServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     */
    private function _streamService(string $sessionId, string $streamId = null): StreamServiceContract
    {
        $this->linkParameters__()->push(collect(['sessionId' => $sessionId]));

        $this->sessionService__()->workWith($sessionId);

        if ($streamId !== null) {
            $this->sessionService__()->streamService()->workWith($streamId);
        }

        return $this->sessionService__()->streamService();
    }

    /**
     * @param FileReceiver $receiver
     * @param string       $sessionId
     * @param string       $streamType
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws UploadMissingFileException
     */
    public function upload(
        FileReceiver $receiver,
        string $sessionId,
        string $streamType
    ): Response {
        $this->sessionService__()->workWith($sessionId);

        try {
            $this->sessionService__()->streamService()->workWithType($streamType);

            return $this->sendResponse(new Progress(100));
        } catch (NotFoundException $exception) {
            $save = $this->receive($receiver);

            $this->sessionService__()->touch();

            if ($save->isFinished()) {
                $acknowledge = true;
                $this->linkParameters__()->put(
                    collect(
                        [
                            'sessionId' => $sessionId,
                        ]
                    )
                );
                try {
                    $this->sessionService__()->streamService()->create($save->getFile(), $streamType);
                } catch (UploadException $exception) {
                    $acknowledge = false;
                } catch (RuntimeException $exception) {
                    @unlink($save->getFile()->getPathname());

                    return $this->sendResponse(new Progress(100));
                }

                @unlink($save->getFile()->getPathname());

                return $this->acknowledgeResponse($acknowledge, 201);
            }

            return $this->sendResponse(new Progress($save->handler()->getPercentageDone()));
        }
    }

    /**
     * @param FileReceiver $receiver
     * @param string       $sessionId
     * @param string       $streamType
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     * @throws UploadMissingFileException
     */
    public function partialReceive(
        FileReceiver $receiver,
        string $sessionId,
        string $streamType
    ): Response {
        $this->sessionService__()->workWith($sessionId);

        $save = $this->receive($receiver);

        if ($save->isFinished()) {
            $acknowledge = true;

            try {
                $this->sessionService__()->streamService()->receivePartial($streamType, $save->getFile());
            } catch (UploadException $exception) {
                $acknowledge = false;
            } catch (RuntimeException $exception) {
                @unlink($save->getFile()->getPathname());

                return $this->sendResponse(new Progress(100));
            }

            @unlink($save->getFile()->getPathname());

            return $this->acknowledgeResponse($acknowledge, 201);
        }

        return $this->sendResponse(new Progress($save->handler()->getPercentageDone()));
    }

    /**
     * @param string $sessionId
     * @param string $streamType
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws UploadException
     */
    public function partialMerge(
        string $sessionId,
        string $streamType
    ): Response {
        $this->linkParameters__()->put(
            collect(
                [
                    'sessionId' => $sessionId,
                ]
            )
        );

        $this->sessionService__()->workWith($sessionId)->streamService()->mergePartial($streamType);

        return $this->acknowledgeResponse(true, 201);
    }
}
