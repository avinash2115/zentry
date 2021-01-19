<?php

namespace App\Components\Users\Http\Controllers\Device;

use App\Assistants\QR\Services\Traits\QRServiceTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Device\Events\Broadcast\Connecting\Failed;
use App\Components\Users\Device\Events\Broadcast\Connecting\Refresh;
use App\Components\Users\Device\Events\Broadcast\Connecting\Started;
use App\Components\Users\Exceptions\Device\TokenExpired;
use App\Components\Users\Exceptions\Device\TokenNotFound;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Components\Users\ValueObjects\Device\ConnectingToken;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Middleware\Access\Device\Authenticate;
use Cache;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Device
 */
class Controller extends BaseController
{
    use AuthServiceTrait;
    use QRServiceTrait;
    use UserServiceTrait;
    use DeviceServiceTrait;

    /**
     * @return Response
     * @throws BindingResolutionException|NotFoundException|InvalidArgumentException
     */
    public function index(): Response
    {
        return $this->sendResponse($this->deviceService__()->list());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $token
     *
     * @return Response
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException|TokenExpired|InvalidArgumentException|PropertyNotInit
     * @throws Exception
     */
    public function connectByToken(JsonApi $jsonApi, string $token): Response
    {
        $payload = new ConnectingPayload(
            $jsonApi->attributes()->get('type', ''),
            $jsonApi->attributes()->get('model', ''),
            $jsonApi->attributes()->get('reference', ''),
        );

        $connectingToken = Cache::get($token, '');

        if (!$connectingToken instanceof ConnectingToken) {
            throw new TokenNotFound();
        }

        event(new Started($payload, $connectingToken->userIdentity()));

        if ($connectingToken->isExpired()) {
            event(new Refresh($payload, $connectingToken->userIdentity()));

            throw new TokenExpired();
        }

        try {
            $dto = $this->deviceService__()->create(
                $this->userService__()->workWith($connectingToken->userIdentity())->readonly(),
                $payload
            )->dto();

            Cache::forget($token);

            return $this->sendResponse($dto, 201);
        } catch (BindingResolutionException|NonUniqueResultException|NotFoundException|InvalidArgumentException $exception) {
            event(new Failed($payload, $connectingToken->userIdentity()));

            Cache::forget($token);

            throw $exception;
        }
    }

    /**
     * @param string  $deviceId
     *
     * @return Response
     * @throws BindingResolutionException|NotFoundException|PropertyNotInit|InvalidArgumentException
     */
    public function show(string $deviceId): Response
    {
        return $this->sendResponse($this->deviceService__()->workWith($deviceId)->dto());
    }

    /**
     * @param string  $deviceId
     *
     * @return Response
     * @throws BindingResolutionException|NotFoundException|PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function remove(string $deviceId): Response
    {
        $this->deviceService__()->workWith($deviceId)->remove();

        return $this->acknowledgeResponse();
    }

    /**
     * @return Response
     * @throws BindingResolutionException|NotFoundException|PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws NonUniqueResultException
     */
    public function removeCurrent(): Response
    {
        $reference = $this->authService__()->deviceReference();

        if ($reference === null) {
            throw new RuntimeException(Authenticate::HEADER . ' is not provided.');
        }

        $this->deviceService__()->workWithReference($reference)->remove();

        return $this->acknowledgeResponse();
    }

    /**
     * @return Response
     * @throws BindingResolutionException|NotFoundException|UnexpectedValueException
     */
    public function qr(): Response
    {
        $payload = $this->userService__()->workWith($this->authService__()->user()->identity())->asQRPayload();

        return app(ResponseFactory::class)->make($this->QRService__()->render($payload))->withHeaders(
            ['Content-Type' => 'image/svg+xml']
        );
    }
}
