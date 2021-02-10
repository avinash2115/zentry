<?php

namespace App\Components\Share\Shared\Mutators\DTO;

use App\Components\Share\Shared\SharedDTO;
use App\Components\Share\Shared\SharedReadonlyContract;

/**
 * Class Mutator
 */
final class Mutator
{
    public const TYPE = 'shared';

    /**
     * @param SharedReadonlyContract $entity
     *
     * @return SharedDTO
     */
    public function toDTO(SharedReadonlyContract $entity): SharedDTO
    {
        $dto = new SharedDTO();

        $dto->id = $entity->identity()->toString();
        $dto->type = $entity->type();
        $dto->payload = $entity->payload();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
