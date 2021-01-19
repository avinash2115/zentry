<?php

namespace App\Components\Users\Participant\Goal\Tracker\Mutators\DTO;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Components\Users\Participant\Goal\Tracker\TrackerDTO;
use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use RuntimeException;

/**
 * Class Mutator
 *
 * @package App\Components\Users\Participant\Goal\Tracker\Mutators\DTO
 */
final class Mutator
{
    use FileServiceTrait;
    use SimplifiedDTOTrait;

    public const TYPE = 'users_participants_goals_trackers';

    /**
     * @param TrackerReadonlyContract $entity
     *
     * @return TrackerDTO
     * @throws RuntimeException
     * @throws BindingResolutionException
     */
    public function toDTO(TrackerReadonlyContract $entity): TrackerDTO
    {
        $dto = new TrackerDTO();
        $dto->id = $entity->identity()->toString();
        $dto->name = $entity->name();
        $dto->type = $entity->type();
        $dto->icon = $entity->icon();
        $dto->color = $entity->color();

        $dto->sessions = collect();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());

        return $dto;
    }
}
