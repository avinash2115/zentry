<?php

namespace App\Components\Users\Device\Mutators\DTO;

use App\Components\Users\Device\DeviceDTO;
use App\Components\Users\Device\DeviceReadonlyContract;
use App\Components\Users\User\Mutators\DTO\Mutator as UserMutator;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Mutator
 */
final class Mutator
{
    public const TYPE = 'users_devices';

    /**
     * @param DeviceReadonlyContract $entity
     *
     * @return DeviceDTO
     * @throws BindingResolutionException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function toDTO(DeviceReadonlyContract $entity): DeviceDTO
    {
        $dto = new DeviceDTO();

        $dto->id = $entity->identity()->toString();
        $dto->type = $entity->type();
        $dto->model = $entity->model();
        $dto->reference = $entity->reference();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $dto->user = app()->make(UserMutator::class)->toDTO($entity->user());
        $dto->user->disableLinks();

        return $dto;
    }
}
