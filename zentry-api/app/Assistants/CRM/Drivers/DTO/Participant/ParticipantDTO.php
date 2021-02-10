<?php

namespace App\Assistants\CRM\Drivers\DTO\Participant;

use App\Assistants\Transformers\JsonApi\Traits\IdTrait;
use Illuminate\Support\Collection;

/**
 * Class ParticipantDTO
 *
 * @package App\Assistants\CRM\Drivers\DTO\Participant
 */
class ParticipantDTO
{
    use IdTrait;

    /**
     * @var string
     */
    public string $firstName;

    /**
     * @var string
     */
    public string $lastName;

    /**
     * @var string
     */
    public string $birthDate;

    /**
     * @var string
     */
    public string $gender;

    /**
     * @var string
     */
    public string $districtId;

    /**
     * @var int
     */
    public int $therapyMinutes;

    /**
     * @var string
     */
    public string $therapyTimePeriod;

    /**
     * @var string
     */
    public string $therapyEligibilityType;

    /**
     * @var Collection
     */
    public Collection $goals;

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'birthDate' => $this->birthDate,
            'gender' => $this->gender,
            'districtId' => $this->districtId,
            'therapyMinutes' => $this->therapyMinutes,
            'therapyTimePeriod' => $this->therapyTimePeriod,
            'therapyEligibilityType' => $this->therapyEligibilityType,
            'goals' => $this->goals->toArray(),
        ];
    }
}
