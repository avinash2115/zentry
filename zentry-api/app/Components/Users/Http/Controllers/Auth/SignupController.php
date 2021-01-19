<?php

namespace App\Components\Users\Http\Controllers\Auth;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Http\Controllers\Auth\Traits\AuthControllerTrait;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Http\Controllers\Controller;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class SignupController
 *
 * @package App\Components\Users\Http\Controllers\Auth
 */
class SignupController extends Controller
{
    use AuthServiceTrait;
    use AuthControllerTrait;

    /**
     * @param JsonApi $jsonApi
     *
     * @return Response
     * @throws BindingResolutionException|NonUniqueResultException|InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function signUp(JsonApi $jsonApi): Response
    {
        $attributes = $jsonApi->attributes();

        $credentials = new Credentials(
            new Email($attributes->get('email', '')),
            new HashedPassword($attributes->get('password', '')),
            new HashedPassword($attributes->get('password_repeat', '')),
        );

        $profile = $jsonApi->relation('profile');

        if (!$profile instanceof JsonApi) {
            throw new UnexpectedValueException('First Name and Last Name are mandatory fields');
        }

        $profilePayload = new Payload(
            $profile->attributes()->get('first_name', ''),
            $profile->attributes()->get('last_name', ''),
            $profile->attributes()->get('phone_code', ''),
            $profile->attributes()->get('phone_number', '')
        );

        $token = $this->authService__()->signup($credentials, $profilePayload);

        if ($token) {
            return $this->sendResponse($this->getSessionResponse($token), 201);
        }

        throw new InvalidArgumentException('Wrong credentials');
    }
}