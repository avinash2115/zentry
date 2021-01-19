<?php

namespace App\Components\Users\User\Poi\Mutators\DTO;

use App\Components\Users\User\Poi\PoiDTO;
use App\Components\Users\User\Poi\PoiReadonlyContract;

/**
 * Class Mutator
 */
final class Mutator
{

    public const TYPE = 'users_poi';

    /**
     * @param PoiReadonlyContract $entity
     *
     * @return PoiDTO
     */
    public function toDTO(PoiReadonlyContract $entity): PoiDTO
    {
        $dto = new PoiDTO();

        $dto->id = $entity->identity()->toString();
        $dto->backward = $entity->backward();
        $dto->forward = $entity->forward();
        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
