<?php

namespace App\Components\Users\Team\Repository;

use App\Components\Users\Team\Mutators\DTO\Mutator;
use App\Components\Users\Team\TeamContract;
use App\Components\Users\Team\TeamEntity;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface TeamRepositoryContract
 *
 * @package App\Components\Users\Team\Repository
 */
interface TeamRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = TeamEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return TeamContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): TeamContract;

    /**
     * @return TeamContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?TeamContract;

    /**
     * @param TeamContract $entity
     *
     * @return TeamContract
     * @throws BindingResolutionException
     */
    public function persist(TeamContract $entity): TeamContract;

    /**
     * @param TeamContract $entity
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(TeamContract $entity): bool;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return TeamRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByMemberIds(array $ids, bool $contains = true): TeamRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return TeamRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByOwnerIds(array $ids, bool $contains = true): TeamRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return TeamRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterBySchoolIds(array $ids, bool $contains = true): TeamRepositoryContract;

    /**
     * @param string $id
     *
     * @return TeamRepositoryContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotImplementedException
     * @throws UnexpectedValueException
     */
    public function filterByUserPresence(string $id): TeamRepositoryContract;
}
