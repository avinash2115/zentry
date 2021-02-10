<?php

namespace App\Components\Users\User\DataProvider\Mutators\DTO;

use App\Components\Users\User\DataProvider\DataProviderDTO;
use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;

/**
 * Class Mutator
 */
final class Mutator
{
    public const TYPE = 'users_data_providers';

    /**
     * @param DataProviderReadonlyContract $entity
     *
     * @return DataProviderDTO
     */
    public function toDTO(DataProviderReadonlyContract $entity): DataProviderDTO
    {
        $dto = new DataProviderDTO();

        $dto->id = $entity->identity()->toString();
        $dto->driver = $entity->driver();
        $dto->status = $entity->status();
        $dto->config = $entity->config();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
