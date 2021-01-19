<?php

namespace App\Components\Users\User\CRM\Mutators\DTO;

use App\Components\Users\User\CRM\CRMDTO;
use App\Components\Users\User\CRM\CRMReadonlyContract;

/**
 * Class Mutator
 */
final class Mutator
{
    public const TYPE = 'users_crms';

    /**
     * @param CRMReadonlyContract $entity
     *
     * @return CRMDTO
     */
    public function toDTO(CRMReadonlyContract $entity): CRMDTO
    {
        $dto = new CRMDTO();

        $dto->id = $entity->identity()->toString();
        $dto->driver = $entity->driver();
        $dto->active = $entity->active();
        $dto->notified = $entity->notified();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
