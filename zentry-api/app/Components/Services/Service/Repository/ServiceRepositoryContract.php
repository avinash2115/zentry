<?php

namespace App\Components\Services\Service\Repository;

use App\Components\Services\Service\Mutators\DTO\Mutator;
use App\Components\Services\Service\ServiceContract;
use App\Components\Services\Service\ServiceEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface ServiceRepositoryContract
 *
 * @package App\Components\Services\Service\Repository
 */
interface ServiceRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = ServiceEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return ServiceContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): ServiceContract;

    /**
     * @return ServiceContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?ServiceContract;

    /**
     * @param ServiceContract $entity
     *
     * @return ServiceContract
     * @throws BindingResolutionException
     */
    public function persist(ServiceContract $entity): ServiceContract;

    /**
     * @param ServiceContract $entity
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(ServiceContract $entity): bool;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return ServiceRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByIds(array $values, bool $contains = true): ServiceRepositoryContract;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return ServiceRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByUsersIds(array $values, bool $contains = true): ServiceRepositoryContract;
}
