<?php

namespace App\Components\Sessions\ValueObjects\Progress;

use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;

/**
 * Class Payload
 *
 * @package App\Assistants\QR\ValueObjects
 */
class Payload
{
    use IdentifiableTrait;

    /**
     * @var DateTime
     */
    private DateTime $datetime;

    /**
     * @var ParticipantReadonlyContract
     */
    private ParticipantReadonlyContract $participant;

    /**
     * @var GoalReadonlyContract
     */
    private GoalReadonlyContract $goal;

    /**
     * @var TrackerReadonlyContract
     */
    private TrackerReadonlyContract $tracker;

    /**
     * @param Identity                    $identity
     * @param DateTime                    $datetime
     * @param ParticipantReadonlyContract $participant
     * @param GoalReadonlyContract        $goal
     * @param TrackerReadonlyContract     $tracker
     */
    public function __construct(
        Identity $identity,
        DateTime $datetime,
        ParticipantReadonlyContract $participant,
        GoalReadonlyContract $goal,
        TrackerReadonlyContract $tracker
    ) {
        $this->setIdentity($identity);
        $this->datetime = $datetime;
        $this->participant = $participant;
        $this->goal = $goal;
        $this->tracker = $tracker;
    }

    /**
     * @return DateTime
     */
    public function datetime(): DateTime
    {
        return $this->datetime;
    }

    /**
     * @return ParticipantReadonlyContract
     */
    public function participant(): ParticipantReadonlyContract
    {
        return $this->participant;
    }

    /**
     * @return GoalReadonlyContract
     */
    public function goal(): GoalReadonlyContract
    {
        return $this->goal;
    }

    /**
     * @return TrackerReadonlyContract
     */
    public function tracker(): TrackerReadonlyContract
    {
        return $this->tracker;
    }
}
