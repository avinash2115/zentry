<?php

namespace App\Components\Users\Device\Repository;

use App\Components\Users\Device\Mutators\DTO\Mutator;
use App\Components\Users\Device\DeviceContract;
use App\Components\Users\Device\DeviceEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface DeviceRepositoryContract
 *
 * @package App\Components\Users\Device\Repository
 */
interface DeviceRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = DeviceEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return DeviceContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): DeviceContract;

    /**
     * @return DeviceContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?DeviceContract;

    /**
     * @param DeviceContract $passwordReset
     *
     * @return DeviceContract
     * @throws BindingResolutionException
     */
    public function persist(DeviceContract $passwordReset): DeviceContract;

    /**
     * @param DeviceContract $passwordReset
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(DeviceContract $passwordReset): bool;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return DeviceRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByUsersIds(array $ids, bool $contains = true): DeviceRepositoryContract;

    /**
     * @param array $references
     * @param bool  $contains
     *
     * @return DeviceRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByReferences(array $references, bool $contains = true): DeviceRepositoryContract;
}
