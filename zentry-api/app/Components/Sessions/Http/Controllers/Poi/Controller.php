<?php

namespace App\Components\Sessions\Http\Controllers\Poi;

use App\Assistants\Files\Services\Stream\Video\Stream;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Sessions\Services\Poi\PoiServiceContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Sessions\ValueObjects\Stream\Token;
use App\Components\Share\Services\Shared\Traits\SharedServiceTrait;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Convention\Exceptions\Auth\UnauthorizedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Storage\File\ReadException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Http\Controllers\Controller as BaseController;
use Cache;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response as SResponse;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Sessions\Http\Controllers\Poi
 */
class Controller extends BaseController
{
    use SessionServiceTrait;
    use LinkParametersTrait;
    use ParticipantServiceTrait;
    use SharedServiceTrait;

    /**
     * @param string $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     */
    public function index(string $sessionId): Response
    {
        return $this->sendResponse(
            $this->_poiService($sessionId)->list()
        );
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException|RuntimeException
     */
    public function create(JsonApi $jsonApi, string $sessionId): Response
    {
        $poiService = $this->_poiService($sessionId)->create($jsonApi->attributes()->toArray());

        $jsonApi->relations('participants')->each(
            function (JsonApi $jsonApi) use ($poiService) {
                $raw = $jsonApi->relation('raw');
                if ($raw instanceof JsonApi) {
                    $poiService->participantService()->add(
                        $this->participantService__()->workWith($raw->id())->readonly(),
                        $jsonApi->attributes()->toArray()
                    );
                }
            }
        );

        return $this->sendResponse(
            $poiService->dto()
        );
    }

    /**
     * @param string $sessionId
     * @param string $poiId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     * @throws RuntimeException
     */
    public function show(string $sessionId, string $poiId): Response
    {
        return $this->sendResponse(
            $this->_poiService($sessionId, $poiId)->dto()
        );
    }

    /**
     * @param string $sessionId
     * @param string $poiId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|NotFoundException|PropertyNotInit|UnexpectedValueException
     * @throws DeleteException
     */
    public function remove(string $sessionId, string $poiId): Response
    {
        $this->_poiService($sessionId, $poiId)->remove();

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     * @param string  $poiId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function change(JsonApi $jsonApi, string $sessionId, string $poiId): Response
    {
        return $this->sendResponse(
            $this->_poiService($sessionId, $poiId)->change($jsonApi->attributes()->toArray())->dto()
        );
    }

    /**
     * @param string $sessionId
     * @param string $poiId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function token(string $sessionId, string $poiId): Response
    {
        $token = new Token(IdentityGenerator::next()->toString());

        Cache::put(
            $token->token,
            $this->_poiService($sessionId, $poiId)->readonly()->stream(),
            StreamReadonlyContract::PLAY_TOKEN_TTL
        );

        return $this->sendResponse($token);
    }

    /**
     * @param JsonApi     $jsonApi
     * @param string      $sessionId
     * @param string|null $poiId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function addParticipants(JsonApi $jsonApi, string $sessionId, string $poiId = null): Response
    {
        $poiService = $this->_poiService($sessionId, $poiId);

        $jsonApi->asJsonApiCollection()->each(
            function (JsonApi $jsonApi) use ($poiService) {
                $raw = $jsonApi->relation('raw');
                if ($raw instanceof JsonApi) {
                    $poiService->participantService()->add(
                        $this->participantService__()->workWith($raw->id())->readonly(),
                        $jsonApi->attributes()->toArray()
                    );
                }
            }
        );

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi     $jsonApi
     * @param string      $sessionId
     * @param string|null $poiId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function removeParticipants(JsonApi $jsonApi, string $sessionId, string $poiId = null): Response
    {
        $poiService = $this->_poiService($sessionId, $poiId);

        $jsonApi->asJsonApiCollection()->each(
            function (JsonApi $jsonApi) use ($poiService) {
                $poiService->participantService()->workWith($jsonApi->id())->remove();
            }
        );

        return $this->acknowledgeResponse();
    }

    /**
     * @param Request $request
     * @param string  $sessionId
     * @param string  $poiId
     * @param string  $token
     *
     * @return SResponse
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws UnauthorizedException
     * @throws UnexpectedValueException
     * @throws ReadException
     * @throws FileNotFoundException
     */
    public function play(Request $request, string $sessionId, string $poiId, string $token): SResponse
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
     * @param string $poiId
     *
     * @return Response
     * @throws BindingResolutionException|InvalidArgumentException|RuntimeException|NotFoundException
     */
    public function temporaryUrl(string $sessionId, string $poiId): Response
    {
        return $this->sendResponse($this->_poiService($sessionId, $poiId)->temporaryUrl());
    }

    /**
     * @param string $sessionId
     * @param string $poiId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function share(string $sessionId, string $poiId): Response
    {
        return $this->sendResponse($this->sharedService__()->create($this->_poiService($sessionId, $poiId))->dto());
    }

    /**
     * @param string $sessionId
     * @param string $poiId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function unshare(string $sessionId, string $poiId): Response
    {
        try {
            $this->sharedService__()->create($this->_poiService($sessionId, $poiId))->remove();
        } catch (NotFoundException $exception) {
            report($exception);
        }

        return $this->acknowledgeResponse();
    }

    /**
     * @param string      $sessionId
     * @param string|null $poiId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function transcript(string $sessionId, string $poiId = null): Response
    {
        return $this->sendResponse($this->_poiService($sessionId, $poiId)->injectedList());
    }

    /**
     * @param string      $sessionId
     * @param string|null $poiId
     *
     * @return PoiServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    private function _poiService(string $sessionId, string $poiId = null): PoiServiceContract
    {
        $this->linkParameters__()->put(
            collect(
                [
                    'sessionId' => $sessionId,
                ]
            )
        );

        $this->sessionService__()->workWith($sessionId);

        if ($poiId !== null) {
            $this->sessionService__()->poiService()->workWith($poiId);
        }

        return $this->sessionService__()->poiService();
    }
}
