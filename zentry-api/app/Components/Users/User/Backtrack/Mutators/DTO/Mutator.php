<?php

namespace App\Components\Users\User\Backtrack\Mutators\DTO;

use App\Components\Users\User\Backtrack\BacktrackDTO;
use App\Components\Users\User\Backtrack\BacktrackReadonlyContract;

/**
 * Class Mutator
 */
final class Mutator
{

    public const TYPE = 'users_backtrack';

    /**
     * @param BacktrackReadonlyContract $entity
     *
     * @return BacktrackDTO
     */
    public function toDTO(BacktrackReadonlyContract $entity): BacktrackDTO
    {
        $dto = new BacktrackDTO();

        $dto->id = $entity->identity()->toString();
        $dto->backward = $entity->backward();
        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
