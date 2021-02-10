<?php

namespace App\Components\Users\Services\Auth;

use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface AuthServiceContract
 *
 * @package App\Components\Users\Services\Auth
 */
interface AuthServiceContract
{
    public const SSO_DRIVER_GOOGLE = 'google';

    public const SSO_AVAILABLE_DRIVERS = [
        self::SSO_DRIVER_GOOGLE
    ];

    /**
     * @param Credentials $credentials
     *
     * @return string | null
     */
    public function login(Credentials $credentials): ?string;

    /**
     * @param UserReadonlyContract $user
     *
     * @return bool
     */
    public function loginOnceFromUser(UserReadonlyContract $user): bool;

    /**
     * @param UserReadonlyContract $user
     *
     * @return string
     */
    public function tokenFromUser(UserReadonlyContract $user): string;

    /**
     * @param Credentials $credentials
     * @param Payload     $payload
     *
     * @return string|null
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function signup(Credentials $credentials, Payload $payload): ?string;

    /**
     * logout method
     */
    public function logout(): void;

    /**
     * @return AuthUserServiceContract
     * @throws NotFoundException|UnexpectedValueException
     */
    public function user(): AuthUserServiceContract;

    /**
     * @return string|null
     * @throws NotFoundException|UnexpectedValueException
     */
    public function deviceReference(): ?string;

    /**
     * @return bool
     */
    public function check(): bool;

    /**
     * @return Collection
     */
    public function drivers(): Collection;
}
