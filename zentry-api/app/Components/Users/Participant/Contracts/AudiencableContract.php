<?php

namespace App\Components\Users\Participant\Contracts;

use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\ValueObjects\Identity\Identity;
use RuntimeException;

/**
 * Interface AudiencableContract
 *
 * @package App\Components\Users\Contracts\Participant
 */
interface AudiencableContract extends AudiencableReadonlyContract
{
    /**
     * @param ParticipantReadonlyContract $participant
     */
    public function addParticipant(ParticipantReadonlyContract $participant): void;

    /**
     * @param Identity $identity
     *
     * @return ParticipantReadonlyContract
     */
    public function participantByIdentity(Identity $identity): ParticipantReadonlyContract;

    /**
     * @param ParticipantReadonlyContract $participant
     */
    public function removeParticipant(ParticipantReadonlyContract $participant): void;

    /**
     * @param ParticipantReadonlyContract $participant
     *
     * @throws RuntimeException
     */
    public function checkRemovalAbility(ParticipantReadonlyContract $participant): void;
}
