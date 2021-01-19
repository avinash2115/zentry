<?php

namespace App\Components\Users\Participant\Repository;

use App\Components\Users\Participant\Mutators\DTO\Mutator;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Participant\ParticipantEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface ParticipantRepositoryContract
 *
 * @package App\Components\Users\Participant\Repository
 */
interface ParticipantRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = ParticipantEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return ParticipantContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): ParticipantContract;

    /**
     * @return ParticipantContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?ParticipantContract;

    /**
     * @param ParticipantContract $entity
     *
     * @return ParticipantContract
     * @throws BindingResolutionException
     */
    public function persist(ParticipantContract $entity): ParticipantContract;

    /**
     * @param ParticipantContract $entity
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(ParticipantContract $entity): bool;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return ParticipantRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByIds(array $ids, bool $contains = true): ParticipantRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return ParticipantRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByUserIds(array $ids, bool $contains = true): ParticipantRepositoryContract;

    /**
     * @param array $emails
     * @param bool  $contains
     *
     * @return ParticipantRepositoryContract
     */
    public function filterByEmails(array $emails, bool $contains = true): ParticipantRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return ParticipantRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByTeamIds(array $ids, bool $contains = true): ParticipantRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return ParticipantRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterBySchoolIds(array $ids, bool $contains = true): ParticipantRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return ParticipantRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByGoalsIds(array $ids, bool $contains = true): ParticipantRepositoryContract;

    /**
     * @param string $email
     *
     * @return bool
     * @throws BindingResolutionException|NonUniqueResultException
     */
    public function isExists(string $email): bool;
}
