<?php

namespace App\Components\CRM\Source\Mutators\DTO;

use App\Components\CRM\Source\SourceDTO;
use App\Components\CRM\Source\SourceReadonlyContract;

/**
 * Class Mutator
 *
 * @package App\Components\CRM\Source\Mutators\DTO
 */
final class Mutator
{
    public const TYPE = 'crms_sources';

    /**
     * @param SourceReadonlyContract $entity
     *
     * @return SourceDTO
     */
    public function toDTO(SourceReadonlyContract $entity): SourceDTO
    {
        $dto = new SourceDTO();

        $dto->id = $entity->identity()->toString();
        $dto->sourceId = $entity->sourceId();
        $dto->direction = $entity->direction();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
