<?php

namespace App\Components\Sessions\Session\Goal;

use App\Components\Users\Participant\Goal\GoalReadonlyContract as ParticipantGoalReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Contracts\HasCreatedAt;
use App\Convention\Entities\Contracts\IdentifiableContract;

/**
 * Interface GoalReadonlyContract
 *
 * @package App\Components\Sessions\Session\Goal
 */
interface GoalReadonlyContract extends IdentifiableContract, HasCreatedAt
{
    /**
     * @return ParticipantGoalReadonlyContract
     */
    public function goal(): ParticipantGoalReadonlyContract;

    /**
     * @return ParticipantReadonlyContract
     */
    public function participant(): ParticipantReadonlyContract;
}
