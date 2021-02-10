<?php

namespace App\Components\Sessions\Session\SOAP;

use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\ValueObjects\SOAP\Payload;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class SOAPEntity
 *
 * @package App\Components\Sessions\Session\SOAP
 */
class SOAPEntity implements SOAPContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

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
     * @var SessionReadonlyContract
     */
    private SessionReadonlyContract $session;

    /**
     * @var ParticipantReadonlyContract
     */
    private ParticipantReadonlyContract $participant;

    /**
     * @var GoalReadonlyContract | null
     */
    private ?GoalReadonlyContract $goal;

    /**
     * @param Identity                $identity
     * @param SessionReadonlyContract $session
     * @param Payload                 $payload
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        SessionReadonlyContract $session,
        Payload $payload
    ) {
        $this->setIdentity($identity);
        $this->session = $session;
        $this->present = $payload->isPresent();
        $this->changeRate($payload->rate());
        $this->activity = $payload->activity();
        $this->note = $payload->note();
        $this->plan = $payload->plan();

        $this->participant = $payload->participant();
        $this->goal = $payload->goal();

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function isPresent(): bool
    {
        return $this->present;
    }

    /**
     * @inheritDoc
     */
    public function present(): SOAPContract
    {
        $this->present = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function absent(): SOAPContract
    {
        $this->present = false;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function rate(): string
    {
        return $this->rate;
    }

    /**
     * @inheritDoc
     */
    public function changeRate(string $value): SOAPContract
    {
        if (!in_array($value, SOAPReadonlyContract::RATES_AVAILABLE, true)) {
            throw new InvalidArgumentException("Rate {$value} is not allowed");
        }

        $this->rate = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function activity(): string
    {
        return $this->activity;
    }

    /**
     * @inheritDoc
     */
    public function changeActivity(string $value): SOAPContract
    {
        $this->activity = strEmpty($value) ? '' : $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function note(): string
    {
        return $this->note;
    }

    /**
     * @inheritDoc
     */
    public function changeNote(string $value): SOAPContract
    {
        $this->note = strEmpty($value) ? '' : $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function plan(): string
    {
        return $this->plan;
    }

    /**
     * @inheritDoc
     */
    public function changePlan(string $value): SOAPContract
    {
        $this->plan = strEmpty($value) ? '' : $value;

        return $this;
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
    public function goal(): ?GoalReadonlyContract
    {
        return $this->goal;
    }
}
