<?php

namespace App\Components\Users\Team;

use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Team\Request\RequestContract;
use App\Components\Users\Team\Request\RequestReadonlyContract;
use App\Components\Users\Team\School\SchoolContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use InvalidArgumentException;

/**
 * Interface TeamContract
 *
 * @package App\Components\Users\Team
 */
interface TeamContract extends TeamReadonlyContract
{
    /**
     * @param string $name
     *
     * @return TeamContract
     * @throws InvalidArgumentException
     */
    public function changeName(string $name): TeamContract;

    /**
     * @param string|null $description
     *
     * @return TeamContract
     */
    public function changeDescription(string $description = null): TeamContract;

    /**
     * @param RequestContract $request
     *
     * @return TeamContract
     */
    public function addRequest(RequestContract $request): TeamContract;

    /**
     * @param Identity $identity
     *
     * @return RequestContract
     * @throws NotFoundException
     */
    public function requestByIdentity(Identity $identity): RequestContract;

    /**
     * @param RequestContract $request
     *
     * @return TeamContract
     * @throws NotFoundException
     */
    public function removeRequest(RequestContract $request): TeamContract;

    /**
     * @param UserReadonlyContract $member
     *
     * @return TeamContract
     */
    public function addMember(UserReadonlyContract $member): TeamContract;

    /**
     * @param Identity $identity
     *
     * @return UserReadonlyContract
     * @throws NotFoundException
     */
    public function memberByIdentity(Identity $identity): UserReadonlyContract;

    /**
     * @param UserReadonlyContract $member
     *
     * @return TeamContract
     * @throws NotFoundException
     */
    public function removeMember(UserReadonlyContract $member): TeamContract;

    /**
     * @param ParticipantContract $participant $request
     *
     * @return TeamContract
     */
    public function addParticipant(ParticipantContract $participant): TeamContract;

    /**
     * @param Identity $identity
     *
     * @return ParticipantContract
     * @throws NotFoundException
     */
    public function participantByIdentity(Identity $identity): ParticipantContract;

    /**
     * @param ParticipantContract $participant
     *
     * @return TeamContract
     * @throws NotFoundException
     */
    public function removeParticipant(ParticipantContract $participant): TeamContract;

    /**
     * @param SchoolContract $entity
     *
     * @return TeamContract
     */
    public function addSchool(SchoolContract $entity): TeamContract;

    /**
     * @param Identity $identity
     *
     * @return SchoolContract
     * @throws NotFoundException
     */
    public function schoolByIdentity(Identity $identity): SchoolContract;

    /**
     * @param SchoolContract $entity
     *
     * @return TeamContract
     * @throws NotFoundException
     */
    public function removeSchool(SchoolContract $entity): TeamContract;
}
