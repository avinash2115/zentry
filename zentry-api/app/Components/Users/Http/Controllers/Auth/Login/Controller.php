<?php

namespace App\Components\Users\Http\Controllers\Auth\Login;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Http\Controllers\Auth\Traits\AuthControllerTrait;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Convention\Exceptions\Auth\WrongCredentials;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Auth
 */
class Controller extends BaseController
{
    use AuthServiceTrait;
    use AuthControllerTrait;

    /**
     * @param JsonApi $jsonApi
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     * @throws WrongCredentials
     */
    public function login(JsonApi $jsonApi): Response
    {
        $attributes = $jsonApi->attributes();
        $device = $jsonApi->relation('device');
        $payload = null;

        if ($device instanceof JsonApi) {
            $payload = new ConnectingPayload($device->attributes()->get('type', ''), $device->attributes()->get('model', ''), $device->attributes()->get('reference', ''));
        }

        $credentials = new Credentials(
            new Email($attributes->get('email', '')),
            new HashedPassword($attributes->get('password', '')),
            null,
            filter_var($attributes->get('remember', false), FILTER_VALIDATE_BOOLEAN),
            $payload
        );

        $token = $this->authService__()->login($credentials);

        if ($token) {
            if ($device instanceof JsonApi) {
                return $this->sendResponse($this->authService__()->user()->dto());
            }

            return $this->sendResponse($this->getSessionResponse($token));
        }

        throw new WrongCredentials('Wrong credentials');
    }

    /**
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function logout(): Response
    {
        $this->authService__()->logout();

        return $this->acknowledgeResponse();
    }

    /**
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function refreshToken(): Response
    {
        return $this->acknowledgeResponse();
    }
}
