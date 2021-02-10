<?php

namespace App\Components\Users\Http\Controllers\PasswordReset;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Http\Controllers\Auth\Login\Controller as LoginController;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\PasswordReset\Traits\PasswordResetServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Components\Users\Exceptions\ResetPassword\TokenExpiredException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\PasswordReset
 */
class Controller extends BaseController
{
    use PasswordResetServiceTrait;
    use UserServiceTrait;
    use AuthServiceTrait;

    /**
     * @param Request $request
     *
     * @return Response
     * @throws BindingResolutionException|NotFoundException|NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function create(Request $request): Response
    {
        $this->passwordResetService__()->create(Arr::get($request->get('data'), 'attributes', []));

        return $this->acknowledgeResponse(true, 201);
    }

    /**
     * @param string  $passwordResetId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws TokenExpiredException
     */
    public function show(string $passwordResetId): Response
    {
        return $this->sendResponse($this->passwordResetService__()->workWith($passwordResetId)->dto());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $passwordResetId
     *
     * @return Response
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException|TokenExpiredException|NoResultException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function update(JsonApi $jsonApi, string $passwordResetId): Response
    {
        $passwordReset = $this->passwordResetService__()->workWith($passwordResetId)->readonly();

        $this->passwordResetService__()->setNewPassword($jsonApi->attributes()->toArray())->remove();

        return app()->make(LoginController::class)->login(
            new JsonApi(
                collect(
                    [
                        'data' => [
                            'attributes' => [
                                'email' => $passwordReset->user()->email(),
                                'password' => $jsonApi->attributes()->get('password'),
                            ],
                        ],
                    ]
                )
            )
        );
    }

    /**
     * @param JsonApi $jsonApi
     *
     * @return Response
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException|NoResultException|UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function activate(JsonApi $jsonApi): Response
    {
        $this->userService__()->workWith($this->authService__()->user()->identity())->change(
            $jsonApi->attributes()->toArray()
        );

        return $this->acknowledgeResponse();
    }
}
