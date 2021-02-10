<?php

namespace App\Components\Sessions\ValueObjects\SOAP;

use App\Components\Sessions\Session\SOAP\SOAPReadonlyContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use InvalidArgumentException;

/**
 * Class Payload
 *
 * @package App\Components\Sessions\ValueObjects\SOAP
 */
class Payload
{
    /**
     * @var bool
     */
    private bool $present;

    /**
     * @var string
     */
    private string $rate;

    /**
     * @var string
     */
    private string $activity;

    /**
     * @var string
     */
    private string $note;

    /**
     * @var string
     */
    private string $plan;

    /**
     * @var ParticipantReadonlyContract
     */
    private ParticipantReadonlyContract $participant;

    /**
     * @var GoalReadonlyContract | null
     */
    private ?GoalReadonlyContract $goal;

    /**
     * @param bool                        $present
     * @param string                      $rate
     * @param string                      $activity
     * @param string                      $note
     * @param string                      $plan
     * @param ParticipantReadonlyContract $participant
     * @param GoalReadonlyContract|null   $goal
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        bool $present,
        string $rate,
        string $activity,
        string $note,
        string $plan,
        ParticipantReadonlyContract $participant,
        ?GoalReadonlyContract $goal = null
    ) {
        $this->present = $present;
        $this->setRate($rate);
        $this->activity = strEmpty($activity) ? '' : $activity;
        $this->note = strEmpty($note) ? '' : $note;
        $this->plan = strEmpty($plan) ? '' : $plan;
        $this->participant = $participant;
        $this->goal = $goal;
    }

    /**
     * @return bool
     */
    public function isPresent(): bool
    {
        return $this->present;
    }

    /**
     * @return string
     */
    public function rate(): string
    {
        return $this->rate;
    }

    /**
     * @param string $value
     *
     * @return Payload
     * @throws InvalidArgumentException
     */
    private function setRate(string $value): Payload
    {
        if (strEmpty($value)) {
            $value = SOAPReadonlyContract::RATE_NO_PROGRESS;
        }

        if (!in_array($value, SOAPReadonlyContract::RATES_AVAILABLE, true)) {
            throw new InvalidArgumentException("Rate {$value} is not allowed");
        }

        $this->rate = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function activity(): string
    {
        return $this->activity;
    }

    /**
     * @return string
     */
    public function note(): string
    {
        return $this->note;
    }

    /**
     * @return string
     */
    public function plan(): string
    {
        return $this->plan;
    }

    /**
     * @return ParticipantReadonlyContract
     */
    public function participant(): ParticipantReadonlyContract
    {
        return $this->participant;
    }

    /**
     * @return GoalReadonlyContract|null
     */
    public function goal(): ?GoalReadonlyContract
    {
        return $this->goal;
    }
}
