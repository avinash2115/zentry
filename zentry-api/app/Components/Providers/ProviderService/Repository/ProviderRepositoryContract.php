<?php

namespace App\Components\Providers\ProviderService\Repository;

use App\Components\Providers\ProviderService\Mutators\DTO\Mutator;
use App\Components\Providers\ProviderService\ProviderContract;
use App\Components\Providers\ProviderService\ProviderEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface ProviderRepositoryContract
 *
 * @package App\Components\Providers\ProviderService\Repository
 */
interface ProviderRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = ProviderEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return ProviderContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): ProviderContract;

    /**
     * @return ProviderContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?ProviderContract;

    /**
     * @param ProviderContract $entity
     *
     * @return ProviderContract
     * @throws BindingResolutionException
     */
    public function persist(ProviderContract $entity): ProviderContract;

    /**
     * @param ProviderContract $entity
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(ProviderContract $entity): bool;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return ProviderRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByIds(array $values, bool $contains = true): ProviderRepositoryContract;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return ProviderRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByUsersIds(array $values, bool $contains = true): ProviderRepositoryContract;
}
