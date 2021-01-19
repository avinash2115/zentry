<?php

namespace App\Components\Users\User\Profile\Mutators\DTO;

use App\Components\Users\User\Profile\ProfileContract;
use App\Components\Users\User\Profile\ProfileDTO;
use App\Components\Users\User\Profile\ProfileReadonlyContract;

/**
 * Class Mutator
 *
 * @package App\Components\Users\User\Profile\Mutators\DTO
 */
final class Mutator
{
    public const TYPE = 'users_profile';

    /**
     * @param ProfileReadonlyContract $entity
     *
     * @return ProfileDTO
     */
    public function toDTO(ProfileReadonlyContract $entity): ProfileDTO
    {
        $dto = new ProfileDTO();
        $dto->id = $entity->identity()->toString();

        $dto->firstName = $entity->firstName();
        $dto->lastName = $entity->lastName();
        $dto->phoneCode = $entity->phoneCode();
        $dto->phoneNumber = $entity->phoneNumber();
        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
