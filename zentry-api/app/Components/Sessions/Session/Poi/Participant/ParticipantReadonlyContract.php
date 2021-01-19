<?php

namespace App\Components\Sessions\Session\Poi\Participant;

use App\Components\Users\Participant\ParticipantReadonlyContract as UsersParticipantReadonlyContract;
use DateTime;

/**
 * Interface ParticipantReadonlyContract
 *
 * @package App\Components\Sessions\Session\Poi\Participant
 */
interface ParticipantReadonlyContract extends UsersParticipantReadonlyContract
{
    /**
     * @return UsersParticipantReadonlyContract
     */
    public function raw(): UsersParticipantReadonlyContract;

    /**
     * @return DateTime
     */
    public function startedAt(): DateTime;

    /**
     * @return DateTime
     */
    public function endedAt(): DateTime;
}
