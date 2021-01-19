<?php

namespace App\Components\Sessions\Session\Progress;

use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use DateTime;

/**
 * Interface ProgressReadonlyContract
 *
 * @package App\Components\Sessions\Session\Progress
 */
interface ProgressReadonlyContract extends IdentifiableContract
{
    /**
     * @return DateTime
     */
    public function datetime(): DateTime;

    /**
     * @return ParticipantReadonlyContract
     */
    public function participant(): ParticipantReadonlyContract;

    /**
     * @return GoalReadonlyContract
     */
    public function goal(): GoalReadonlyContract;

    /**
     * @return TrackerReadonlyContract
     */
    public function tracker(): TrackerReadonlyContract;

    /**
     * @return PoiReadonlyContract|null
     */
    public function poi(): ?PoiReadonlyContract;
}
