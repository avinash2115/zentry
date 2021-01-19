<?php

namespace App\Components\Users\Services\Auth;

use App\Components\Users\User\UserDTO;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Interface AuthUserServiceContract
 *
 * @package App\Components\Users\Services\Auth
 */
interface AuthUserServiceContract extends JWTSubject, Authenticatable
{
    /**
     * @param string $identity
     *
     * @return AuthUserServiceContract
     * @throws BindingResolutionException|NotFoundException
     */
    public function workWith(string $identity): AuthUserServiceContract;

    /**
     * @return Identity
     */
    public function identity(): Identity;

    /**
     * @return UserReadonlyContract
     */
    public function readonly(): UserReadonlyContract;

    /**
     * @return UserDTO
     * @throws BindingResolutionException
     */
    public function dto(): UserDTO;

    /**
     * @param string $identity
     *
     * @return AuthUserServiceContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function retrieveByIdentity(string $identity): AuthUserServiceContract;

    /**
     * @param Collection $filters
     *
     * @return AuthUserServiceContract
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException
     */
    public function workWithByFilters(Collection $filters): AuthUserServiceContract;

    /**
     * @param ConnectingPayload $payload
     *
     * @return AuthUserServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws NonUniqueResultException
     * @throws InvalidArgumentException
     */
    public function connect(ConnectingPayload $payload): AuthUserServiceContract;
}
