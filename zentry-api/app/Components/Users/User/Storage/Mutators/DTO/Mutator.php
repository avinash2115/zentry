<?php

namespace App\Components\Users\User\Storage\Mutators\DTO;

use App\Components\Users\User\Storage\StorageDTO;
use App\Components\Users\User\Storage\StorageReadonlyContract;

/**
 * Class Mutator
 */
final class Mutator
{
    public const TYPE = 'users_storages';

    /**
     * @param StorageReadonlyContract $entity
     *
     * @return StorageDTO
     */
    public function toDTO(StorageReadonlyContract $entity): StorageDTO
    {
        $dto = new StorageDTO();

        $dto->id = $entity->identity()->toString();
        $dto->driver = $entity->driver();
        $dto->name = $entity->name();
        $dto->enabled = $entity->enabled();
        $dto->available = $entity->available();
        $dto->used = $entity->used();
        $dto->capacity = $entity->capacity();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
