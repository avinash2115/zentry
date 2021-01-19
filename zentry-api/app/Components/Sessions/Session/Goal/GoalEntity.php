<?php

namespace App\Components\Sessions\Session\Goal;

use App\Components\Sessions\Session\SessionContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract as ParticipantGoalReadonlyContract;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;

/**
 * Class GoalEntity
 *
 * @package App\Components\Sessions\Session\Goal
 */
class GoalEntity implements GoalContract
{
    use IdentifiableTrait;

    /**
     * @var DateTime
     */
    public DateTime $createdAt;

    /**
     * @var ParticipantContract
     */
    private ParticipantContract $participant;

    /**
     * @var ParticipantGoalReadonlyContract
     */
    private ParticipantGoalReadonlyContract $goal;

    /**
     * @var SessionContract
     */
    private SessionContract $session;

    /**
     * @param Identity                        $identity
     * @param SessionContract                 $session
     * @param ParticipantContract             $participant
     * @param ParticipantGoalReadonlyContract $goal
     * @param DateTime                        $createdAt
     */
    public function __construct(
        Identity $identity,
        SessionContract $session,
        ParticipantContract $participant,
        ParticipantGoalReadonlyContract $goal,
        DateTime $createdAt
    ) {
        $this->setIdentity($identity);
        $this->session = $session;
        $this->participant = $participant;
        $this->goal = $goal;
        $this->createdAt = $createdAt;
    }

    /**
     * @inheritDoc
     */
    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @inheritDoc
     */
    public function goal(): ParticipantGoalReadonlyContract
    {
        return $this->goal;
    }

    /**
     * @inheritDoc
     */
    public function participant(): ParticipantReadonlyContract
    {
        return $this->participant;
    }
}
