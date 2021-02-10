<?php

namespace App\Components\Users\Participant\Therapy\Mutators\DTO;

use App\Components\Users\Participant\Therapy\TherapyDTO;
use App\Components\Users\Participant\Therapy\TherapyReadonlyContract;
use InvalidArgumentException;

/**
 * Class Mutator
 *
 * @package App\Components\Users\Participant\Therapy\Mutators\DTO
 */
final class Mutator
{
    public const TYPE = 'users_participants_therapies';

    /**
     * @param TherapyReadonlyContract $entity
     *
     * @return TherapyDTO
     * @throws InvalidArgumentException
     */
    public function toDTO(TherapyReadonlyContract $entity): TherapyDTO
    {
        $dto = new TherapyDTO();

        $dto->id = $entity->identity()->toString();
        $dto->diagnosis = $entity->diagnosis();
        $dto->frequency = $entity->frequency();
        $dto->eligibility = $entity->eligibility();
        $dto->sessionsAmountPlanned = $entity->sessionsAmountPlanned();
        $dto->treatmentAmountPlanned = $entity->treatmentAmountPlanned();
        $dto->notes = $entity->notes();
        $dto->privateNotes = $entity->privateNotes();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
