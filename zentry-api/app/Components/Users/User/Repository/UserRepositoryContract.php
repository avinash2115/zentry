<?php

namespace App\Components\Users\User\Repository;

use App\Components\Users\User\Mutators\DTO\Mutator;
use App\Components\Users\User\UserContract;
use App\Components\Users\User\UserEntity;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface UserRepositoryContract
 *
 * @package App\Components\Users\User\Repository
 */
interface UserRepositoryContract extends RepositoryContract
{
    const CLASS_NAME = UserEntity::class;

    const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return UserContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): UserContract;

    /**
     * @return UserContract|null
     * @throws BindingResolutionException|NonUniqueResultException
     */
    public function getOne(): ?UserContract;

    /**
     * @param UserContract $user
     *
     * @return UserContract
     * @throws BindingResolutionException
     */
    public function persist(UserContract $user): UserContract;

    /**
     * @param UserContract $user
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(UserContract $user): bool;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return UserRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByIds(array $ids, bool $contains = true): UserRepositoryContract;

    /**
     * @param array $emails
     * @param bool  $contains
     *
     * @return UserRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByEmails(array $emails, bool $contains = true): UserRepositoryContract;

    /**
     * @param array $drivers
     * @param bool  $contains
     * @param bool  $enabled
     *
     * @return UserRepositoryContract
     * @throws BindingResolutionException
     * @throws NotImplementedException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function filterByStorageDrivers(
        array $drivers,
        bool $contains = true,
        bool $enabled = true
    ): UserRepositoryContract;

    /**
     * @param array $drivers
     * @param bool  $contains
     *
     * @return UserRepositoryContract
     * @throws BindingResolutionException
     * @throws NotImplementedException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function filterByDataProviders(
        array $drivers,
        bool $contains = true
    ): UserRepositoryContract;

    /**
     * @param array $statuses
     * @param bool  $contains
     *
     * @return UserRepositoryContract
     * @throws BindingResolutionException
     * @throws NotImplementedException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function filterByDataProvidersStatuses(
        array $statuses,
        bool $contains = true
    ): UserRepositoryContract;

    /**
     * @param string $email
     *
     * @return bool
     * @throws BindingResolutionException|NonUniqueResultException
     */
    public function isExists(string $email): bool;
}
