<?php

namespace App\Components\Sessions\Session\Progress;

use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\ValueObjects\Progress\Payload;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use DateTime;

/**
 * Class ProgressEntity
 *
 * @package App\Components\Sessions\Session\Progress
 */
class ProgressEntity implements ProgressContract
{
    use IdentifiableTrait;

    /**
     * @var DateTime
     */
    protected DateTime $datetime;

    /**
     * @var ParticipantReadonlyContract
     */
    protected ParticipantReadonlyContract $participant;

    /**
     * @var GoalReadonlyContract
     */
    protected GoalReadonlyContract $goal;

    /**
     * @var TrackerReadonlyContract
     */
    protected TrackerReadonlyContract $tracker;

    /**
     * @var SessionReadonlyContract
     */
    private SessionReadonlyContract $session;

    /**
     * @var PoiReadonlyContract|null
     */
    private ?PoiReadonlyContract $poi;

    /**
     * @param Payload                  $payload
     * @param SessionReadonlyContract  $session
     * @param PoiReadonlyContract|null $poi
     */
    public function __construct(
        Payload $payload,
        SessionReadonlyContract $session,
        ?PoiReadonlyContract $poi = null
    ) {
        $this->setIdentity($payload->identity());
        $this->datetime = $payload->datetime();
        $this->participant = $payload->participant();
        $this->goal = $payload->goal();
        $this->tracker = $payload->tracker();
        $this->session = $session;
        $this->poi = $poi;
    }

    /**
     * @inheritDoc
     */
    public function datetime(): DateTime
    {
        return $this->datetime;
    }

    /**
     * @inheritDoc
     */
    public function participant(): ParticipantReadonlyContract
    {
        return $this->participant;
    }

    /**
     * @inheritDoc
     */
    public function goal(): GoalReadonlyContract
    {
        return $this->goal;
    }

    /**
     * @inheritDoc
     */
    public function tracker(): TrackerReadonlyContract
    {
        return $this->tracker;
    }

    /**
     * @inheritDoc
     */
    public function poi(): ?PoiReadonlyContract
    {
        return $this->poi;
    }
}
