<?php

namespace App\Components\Users\PasswordReset\Repository;

use App\Components\Users\PasswordReset\Mutators\DTO\Mutator;
use App\Components\Users\PasswordReset\PasswordResetContract;
use App\Components\Users\PasswordReset\PasswordResetEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface PasswordResetRepositoryContract
 *
 * @package App\Components\Users\PasswordReset\Repository
 */
interface PasswordResetRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = PasswordResetEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return PasswordResetContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): PasswordResetContract;

    /**
     * @return PasswordResetContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?PasswordResetContract;

    /**
     * @param PasswordResetContract $passwordReset
     *
     * @return PasswordResetContract
     * @throws BindingResolutionException
     */
    public function persist(PasswordResetContract $passwordReset): PasswordResetContract;

    /**
     * @param PasswordResetContract $passwordReset
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(PasswordResetContract $passwordReset): bool;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return PasswordResetRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByUsersIds(array $ids, bool $contains = true): PasswordResetRepositoryContract;
}
