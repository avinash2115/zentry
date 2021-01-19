<?php

namespace App\Components\Users\Http\Controllers\Auth\Login\Token;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Http\Controllers\Auth\Traits\AuthControllerTrait;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Login\Token\Traits\TokenServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Components\Users\Exceptions\ResetPassword\TokenExpiredException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Auth\Login\Token
 */
class Controller extends BaseController
{
    use UserServiceTrait;
    use AuthServiceTrait;
    use TokenServiceTrait;
    use AuthControllerTrait;

    /**
     * @param JsonApi $jsonApi
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws NonUniqueResultException
     */
    public function create(JsonApi $jsonApi): Response
    {
        $this->tokenService__()->create(
            $this->authService__()->user()->readonly(),
            $jsonApi->attributes()->get('referer', '')
        );

        return $this->sendResponse($this->tokenService__()->dto(), 201);
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $tokenId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws TokenExpiredException
     * @throws PermissionDeniedException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function signin(JsonApi $jsonApi, string $tokenId): Response
    {
        $response = $this->getSessionResponse(
            $this->tokenService__()->workWith($tokenId)->login($jsonApi->attributes()->get('referer', ''))
        );

        return $this->sendResponse($response);
    }
}
