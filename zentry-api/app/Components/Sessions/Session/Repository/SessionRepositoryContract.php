<?php

namespace App\Components\Sessions\Session\Repository;

use App\Components\Sessions\Session\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\SessionEntity;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface SessionRepositoryContract
 *
 * @package App\Components\Sessions\Session\Repository
 */
interface SessionRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = SessionEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return SessionContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): SessionContract;

    /**
     * @return SessionContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?SessionContract;

    /**
     * @param SessionContract $entity
     *
     * @return SessionContract
     * @throws BindingResolutionException
     */
    public function persist(SessionContract $entity): SessionContract;

    /**
     * @param SessionContract $entity
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(SessionContract $entity): bool;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByIds(array $values, bool $contains = true): SessionRepositoryContract;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByUsersIds(array $values, bool $contains = true): SessionRepositoryContract;

    /**
     * @param bool $isStarted
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByStarted(bool $isStarted = true): SessionRepositoryContract;

    /**
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByNullableEndedAt(): SessionRepositoryContract;

    /**
     * @param bool $isEnded
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByEnded(bool $isEnded = true): SessionRepositoryContract;

    /**
     * @param bool $scheduled
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByScheduledOn(bool $scheduled = true): SessionRepositoryContract;

    /**
     * @param DateTime $gte
     * @param DateTime $lte
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByScheduledOnRange(DateTime $gte = null, DateTime $lte = null): SessionRepositoryContract;

    /**
     * @param DateTime $gte
     * @param DateTime $lte
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByScheduledToRange(DateTime $gte = null, DateTime $lte = null): SessionRepositoryContract;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException|NotImplementedException
     */
    public function filterByParticipantsIds(array $values, bool $contains = true): SessionRepositoryContract;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return SessionRepositoryContract
     */
    public function filterByPoisIds(array $values, bool $contains = true): SessionRepositoryContract;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByTypes(array $values, bool $contains = true): SessionRepositoryContract;

    /**
     * @param array $values
     * @param bool  $contains
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByStatuses(array $values, bool $contains = true): SessionRepositoryContract;

    /**
     * @param bool $referencable
     *
     * @return SessionRepositoryContract
     * @throws BindingResolutionException|InvalidArgumentException|UnexpectedValueException
     */
    public function filterByReferencable(bool $referencable = true): SessionRepositoryContract;
}
