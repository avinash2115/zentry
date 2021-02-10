<?php

namespace App\Components\CRM\SyncLog\Repository;

use App\Components\CRM\SyncLog\Mutators\DTO\Mutator;
use App\Components\CRM\SyncLog\SyncLogContract;
use App\Components\CRM\SyncLog\SyncLogEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface SyncLogRepositoryContract
 *
 * @package App\Components\CRM\SyncLog\Repository
 */
interface SyncLogRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = SyncLogEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return SyncLogContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): SyncLogContract;

    /**
     * @return SyncLogContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?SyncLogContract;

    /**
     * @param SyncLogContract $entity
     *
     * @return SyncLogContract
     * @throws BindingResolutionException
     */
    public function persist(SyncLogContract $entity): SyncLogContract;

    /**
     * @param SyncLogContract $entity
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(SyncLogContract $entity): bool;

    /**
     * @param string $id
     * @param bool   $contains
     *
     * @return SyncLogRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByCRM(string $id, bool $contains = true): SyncLogRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return SyncLogRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByTypes(array $ids, bool $contains = true): SyncLogRepositoryContract;

}
