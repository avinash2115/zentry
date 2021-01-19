<?php

namespace App\Components\Users\Team\School;

use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Team\TeamContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use InvalidArgumentException;

/**
 * Interface SchoolContract
 *
 * @package App\Components\Users\Team\School
 */
interface SchoolContract extends SchoolReadonlyContract
{

    /**
     * @param string $name
     *
     * @return SchoolContract
     * @throws InvalidArgumentException
     */
    public function changeName(string $name): SchoolContract;

    /**
     * @param bool $available
     *
     * @return SchoolContract
     */

    public function changeAvailable(bool $available): SchoolContract;
    /**
     * @param string|null $streetAddress
     *
     * @return SchoolContract
     */
    public function changeStreetAddress(?string $streetAddress): SchoolContract;

    /**
     * @param string|null $city
     *
     * @return SchoolContract
     */
    public function changeCity(?string $city): SchoolContract;

    /**
     * @param string|null $state
     *
     * @return SchoolContract
     */
    public function changeState(?string $state): SchoolContract;

    /**
     * @param string|null $zip
     *
     * @return SchoolContract
     */
    public function changeZip(?string $zip): SchoolContract;

    /**
     * @param ParticipantReadonlyContract $participant
     *
     * @return SchoolContract
     */
    public function addParticipant(ParticipantReadonlyContract $participant): SchoolContract;

    /**
     * @param Identity $identity
     *
     * @return ParticipantReadonlyContract
     * @throws NotFoundException
     */
    public function participantByIdentity(Identity $identity): ParticipantReadonlyContract;

    /**
     * @param ParticipantReadonlyContract $participant
     *
     * @return SchoolContract
     * @throws NotFoundException
     */
    public function removeParticipant(ParticipantReadonlyContract $participant): SchoolContract;

    /**
     * @param TeamContract $team
     *
     * @return SchoolContract
     */
    public function moveTo(TeamContract $team): SchoolContract;
}
