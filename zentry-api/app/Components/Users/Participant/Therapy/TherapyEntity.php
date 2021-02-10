<?php

namespace App\Components\Users\Participant\Therapy;

use App\Components\Users\Participant\ParticipantContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class TherapyEntity
 *
 * @package App\Components\Users\Participant\Therapy
 */
class TherapyEntity implements TherapyContract
{
    use IdentifiableTrait;
    use TimestampableTrait;
    use CollectibleTrait;

    /**
     * @var string
     */
    private string $diagnosis;

    /**
     * @var string
     */
    private string $frequency;

    /**
     * @var string
     */
    private string $eligibility;

    /**
     * @var int
     */
    private int $sessionsAmountPlanned;

    /**
     * @var int
     */
    private int $treatmentAmountPlanned;

    /**
     * @var string
     */
    private string $notes;

    /**
     * @var string
     */
    private string $privateNotes;

    /**
     * @var ParticipantContract
     */
    private ParticipantContract $participant;

    /**
     * @param Identity            $identity
     * @param ParticipantContract $participant
     * @param string              $diagnosis
     * @param string              $frequency
     * @param string              $eligibility
     * @param int                 $sessionsAmountPlanned
     * @param int                 $treatmentAmountPlanned
     * @param string              $notes
     * @param string              $privateNotes
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function __construct(
        Identity $identity,
        ParticipantContract $participant,
        string $diagnosis,
        string $frequency,
        string $eligibility,
        int $sessionsAmountPlanned,
        int $treatmentAmountPlanned,
        string $notes = '',
        string $privateNotes = ''
    ) {
        $this->setIdentity($identity);
        $this->changeDiagnosis($diagnosis);
        $this->changeFrequency($frequency);
        $this->changeEligibility($eligibility);
        $this->changeSessionsAmountPlanned($sessionsAmountPlanned);
        $this->changeTreatmentAmountPlanned($treatmentAmountPlanned);
        $this->changeNotes($notes);
        $this->changePrivateNotes($privateNotes);

        $this->participant = $participant;

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function diagnosis(): string
    {
        return $this->diagnosis;
    }

    /**
     * @inheritDoc
     */
    public function changeDiagnosis(string $value): TherapyContract
    {
        if (strEmpty($value)) {
            throw new InvalidArgumentException("Diagnosis can't be empty");
        }

        $this->diagnosis = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function frequency(): string
    {
        return $this->frequency;
    }

    /**
     * @inheritDoc
     */
    public function changeFrequency(string $value): TherapyContract
    {
        if (!in_array($value, self::FREQUENCIES_AVAILABLE, true)) {
            throw new InvalidArgumentException("Frequency {$value} is not allowed");
        }

        $this->frequency = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function eligibility(): string
    {
        return $this->eligibility;
    }

    /**
     * @inheritDoc
     */
    public function changeEligibility(string $value): TherapyContract
    {
        if (!in_array($value, self::ELIGIBILITIES_AVAILABLE, true)) {
            throw new InvalidArgumentException("Eligibility {$value} is not allowed");
        }

        $this->eligibility = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sessionsAmountPlanned(): int
    {
        return $this->sessionsAmountPlanned;
    }

    /**
     * @inheritDoc
     */
    public function changeSessionsAmountPlanned(int $value): TherapyContract
    {
        $this->sessionsAmountPlanned = $value < 0 ? 0 : $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function treatmentAmountPlanned(): int
    {
        return $this->treatmentAmountPlanned;
    }

    /**
     * @inheritDoc
     */
    public function changeTreatmentAmountPlanned(int $value): TherapyContract
    {
        $this->treatmentAmountPlanned = $value < 0 ? 0 : $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function notes(): string
    {
        return $this->notes;
    }

    /**
     * @inheritDoc
     */
    public function changeNotes(string $value): TherapyContract
    {
        $this->notes = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function privateNotes(): string
    {
        return $this->privateNotes;
    }

    /**
     * @inheritDoc
     */
    public function changePrivateNotes(string $value): TherapyContract
    {
        $this->privateNotes = $value;

        return $this;
    }
}
