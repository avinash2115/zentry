<?php

namespace App\Components\CRM\Source\Repository;

use App\Components\CRM\Source\Mutators\DTO\Mutator;
use App\Components\CRM\Source\SourceContract;
use App\Components\CRM\Source\SourceEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface SourceRepositoryContract
 *
 * @package App\Components\CRM\Source\Repository
 */
interface SourceRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = SourceEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return SourceContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): SourceContract;

    /**
     * @return SourceContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?SourceContract;

    /**
     * @param SourceContract $entity
     *
     * @return SourceContract
     * @throws BindingResolutionException
     */
    public function persist(SourceContract $entity): SourceContract;

    /**
     * @param SourceContract $entity
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(SourceContract $entity): bool;

    /**
     * @param string $id
     * @param bool   $contains
     *
     * @return SourceRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByCRM(string $id, bool $contains = true): SourceRepositoryContract;

    /**
     * @param string $className
     * @param bool   $contains
     *
     * @return SourceRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByClass(string $className, bool $contains = true): SourceRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return SourceRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByDirections(array $ids, bool $contains = true): SourceRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return SourceRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByOwnerIds(array $ids, bool $contains = true): SourceRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return SourceRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterBySourceIds(array $ids, bool $contains = true): SourceRepositoryContract;

}
