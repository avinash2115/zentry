<?php

namespace App\Components\Users\Providers\Auth;

use App\Components\Users\Services\Auth\AuthUserServiceContract;
use App\Components\Users\Services\Auth\Traits\AuthUserServiceTrait;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\Providers\Auth as JWTAuthContract;

/**
 * Class AuthServiceUserProvider
 *
 * @package App\Components\Users\Providers\Auth
 */
class AuthServiceUserProvider implements UserProvider, JWTAuthContract
{
    use AuthUserServiceTrait;

    const NAME = 'auth_user_service';

    /**
     * @var HasherContract
     */
    protected HasherContract $hasher;

    /**
     * Create a new database user provider.
     *
     * @param HasherContract $hasher
     *
     * @return void
     */
    public function __construct(HasherContract $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * @param mixed $identifier
     *
     * @return AuthUserServiceContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function retrieveById($identifier): AuthUserServiceContract
    {
        return $this->authUserService__()->retrieveByIdentity($identifier);
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return AuthUserServiceContract
     * @throws NotImplementedException
     */
    public function retrieveByToken($identifier, $token): AuthUserServiceContract
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param Authenticatable $entity
     * @param string          $token
     *
     * @return void
     */
    public function updateRememberToken(Authenticatable $entity, $token)
    {
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return AuthUserServiceContract
     * @throws BindingResolutionException|NotFoundException|NonUniqueResultException
     */
    public function retrieveByCredentials(array $credentials): AuthUserServiceContract
    {
        $criteria = collect([]);

        foreach ($credentials as $key => $value) {
            if (!Str::contains((string)$key, 'password')) {
                $criteria->put($key, $value);
            }
        }

        return $this->authUserService__()->workWithByFilters($criteria);
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param Authenticatable $user
     * @param array           $credentials
     *
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return $this->hasher->check($credentials['password'], $user->getAuthPassword());
    }

    /**
     * @param array $credentials
     *
     * @return mixed
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException
     */
    public function byCredentials(array $credentials)
    {
        return $this->retrieveByCredentials($credentials);
    }

    /**
     * Authenticate a user via the id.
     *
     * @param mixed $id
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    public function byId($id)
    {
        try {
            return $this->retrieveById($id);
        } catch (NotFoundException $exception) {
            return false;
        }
    }

    /**
     * Get the currently authenticated user.
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    public function user()
    {
        return $this->authUserService__();
    }
}
