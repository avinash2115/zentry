<?php

namespace App\Components\Share\Shared\Repository;

use App\Components\Share\Shared\Mutators\DTO\Mutator;
use App\Components\Share\Shared\SharedContract;
use App\Components\Share\Shared\SharedEntity;
use App\Components\Share\ValueObjects\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface SharedRepositoryContract
 *
 * @package App\Components\Share\Shared\Repository
 */
interface SharedRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = SharedEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return SharedContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): SharedContract;

    /**
     * @return SharedContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?SharedContract;

    /**
     * @param SharedContract $entity
     *
     * @return SharedContract
     * @throws BindingResolutionException
     */
    public function persist(SharedContract $entity): SharedContract;

    /**
     * @param SharedContract $entity
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(SharedContract $entity): bool;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return SharedRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterById(array $ids, bool $contains = true): SharedRepositoryContract;

    /**
     * @param Payload $payload
     *
     * @return SharedRepositoryContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function filterByPayload(Payload $payload): SharedRepositoryContract;
}
