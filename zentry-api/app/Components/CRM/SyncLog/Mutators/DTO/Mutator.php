<?php

namespace App\Components\CRM\SyncLog\Mutators\DTO;

use App\Components\CRM\SyncLog\SyncLogDTO;
use App\Components\CRM\SyncLog\SyncLogReadonlyContract;

/**
 * Class Mutator
 *
 * @package App\Components\CRM\SyncLog\Mutators\DTO
 */
final class Mutator
{
    public const TYPE = 'crms_sync_logs';

    /**
     * @param SyncLogReadonlyContract $entity
     *
     * @return SyncLogDTO
     */
    public function toDTO(SyncLogReadonlyContract $entity): SyncLogDTO
    {
        $dto = new SyncLogDTO();

        $dto->id = $entity->identity()->toString();
        $dto->syncLogType = $entity->type();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());

        return $dto;
    }
}
