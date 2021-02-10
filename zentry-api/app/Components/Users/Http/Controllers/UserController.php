<?php

namespace App\Components\Users\Http\Controllers;

use App\Assistants\Transformers\JsonApi\LinkParameters;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class UserController
 *
 * @package App\Components\Users\Http\Controllers\Auth
 */
class UserController extends Controller
{
    use UserServiceTrait;
    use AuthServiceTrait;
    use LinkParametersTrait;

    /**
     * @param Request $request
     * @param string  $userId
     *
     * @return Response
     * @throws NotFoundException|BindingResolutionException
     */
    public function show(Request $request, string $userId): Response
    {
        return $this->sendResponse($this->userService__()->workWith($userId)->dto());
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $userId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws NotFoundException
     */
    public function change(JsonApi $jsonApi, string $userId): Response
    {
        $this->linkParameters__()->put(collect(['userId' => $userId]));

        $this->userService__()->workWith($userId);

        $this->userService__()->change(
            [
                'email' => $jsonApi->attributes()->get('email', $this->userService__()->readonly()->email()),
                'password' => $jsonApi->attributes()->get('password'),
                'password_repeat' => $jsonApi->attributes()->get('password_repeat')
            ]
        );

        $profile = $jsonApi->relation('profile');

        if ($profile instanceof JsonApi) {
            $this->userService__()->changeProfile(
                new Payload(
                    $profile->attributes()->get(
                        'first_name',
                        $this->userService__()->readonly()->profileReadonly()->firstName()
                    ), $profile->attributes()->get(
                    'last_name',
                    $this->userService__()->readonly()->profileReadonly()->lastName()
                ), $profile->attributes()->get(
                    'phone_code',
                    $this->userService__()->readonly()->profileReadonly()->phoneCode()
                ), $profile->attributes()->get(
                    'phone_number',
                    $this->userService__()->readonly()->profileReadonly()->phoneNumber()
                )
                )
            );
        }

        return $this->sendResponse($this->userService__()->dto());
    }

    /**
     * @param Request        $request
     * @param LinkParameters $linkParameters
     *
     * @return Response
     * @throws BindingResolutionException|NotFoundException|UnexpectedValueException
     */
    public function current(Request $request, LinkParameters $linkParameters): Response
    {
        $linkParameters->push($this->authService__()->user()->identity()->toString());

        return $this->show($request, $this->authService__()->user()->identity());
    }
}