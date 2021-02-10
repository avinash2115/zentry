<?php

namespace App\Components\Sessions\Session\Stream\Mutators\DTO;

use App\Components\Sessions\Session\Stream\StreamDTO;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;

/**
 * Class Mutator
 */
final class Mutator
{
    public const TYPE = 'sessions_streams';

    /**
     * @param StreamReadonlyContract $entity
     *
     * @return StreamDTO
     */
    public function toDTO(StreamReadonlyContract $entity): StreamDTO
    {
        $dto = new StreamDTO();
        $dto->id = $entity->identity()->toString();
        $dto->type = $entity->type();

        $dto->convertProgress = $entity->convertProgress();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());

        return $dto;
    }
}
