<?php

namespace App\Components\Users\Http\Controllers\Auth\SSO;

use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Users\Http\Controllers\Auth\Traits\AuthControllerTrait;
use App\Components\Users\Services\Auth\AuthServiceContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\SSO\SSOReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Arr;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Log;
use RuntimeException;

/**
 * Class Controller
 *
 * @package App\Components\Users\Http\Controllers\Auth\SSO
 */
class Controller extends BaseController
{
    use AuthServiceTrait;
    use AuthControllerTrait;
    use UserServiceTrait;

    /**
     * @param JsonApi $jsonApi
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws PropertyNotInit
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function make(JsonApi $jsonApi): Response
    {
        $driver = $jsonApi->attributes()->get('driver');
        $authToken = Arr::get($jsonApi->attributes()->get('config', []), 'authToken');

        if (!in_array($driver, AuthServiceContract::SSO_AVAILABLE_DRIVERS, true)) {
            throw new InvalidArgumentException("Driver {$driver} is now allowed");
        }

        $provider = Socialite::driver($driver);

        $provider->stateless();
        $socialiteUser = $provider->userFromToken($authToken);

        if (!$socialiteUser instanceof SocialiteUser) {
            Log::error("Socialite doesn't returns User");
            throw new InvalidArgumentException('Server error');
        }

        try {
            $user = $this->userService__()->workWithByEmail($socialiteUser->getEmail())->readonly();
        } catch (NotFoundException $exception) {
            $password = new HashedPassword(Str::random(8));

            $credentials = new Credentials(
                new Email($socialiteUser->getEmail()), $password, $password,
            );

            $exploded = explode(' ', $socialiteUser->getName());
            $firstName = array_shift($exploded);

            $token = $this->authService__()->signup($credentials, new Payload($firstName, implode(' ', $exploded)));

            if ($token) {
                return $this->sendResponse($this->getSessionResponse($token), 201);
            }

            Log::error('Empty jwt token at the sso sign up');
            throw new InvalidArgumentException('Server error');
        }

        return $this->sendResponse($this->getSessionResponse($this->authService__()->tokenFromUser($user)));
    }

    /**
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function drivers(): Response
    {
        return $this->sendResponse(
            $this->authService__()->drivers()
        );
    }
}
