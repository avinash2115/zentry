<?php

namespace App\Components\Users\PasswordReset\Mutators\DTO;

use App\Components\Users\PasswordReset\PasswordResetDTO;
use App\Components\Users\PasswordReset\PasswordResetReadonlyContract;
use App\Components\Users\User\Mutators\DTO\Mutator as UserMutator;
use App\Components\Users\User\UserReadonlyContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class Mutator
 */
final class Mutator
{
    public const TYPE = 'password_resets';

    /**
     * @param PasswordResetReadonlyContract $entity
     *
     * @return PasswordResetDTO
     * @throws BindingResolutionException
     */
    public function toDTO(PasswordResetReadonlyContract $entity): PasswordResetDTO
    {
        $dto = new PasswordResetDTO();
        $dto->id = $entity->identity()->toString();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $dto->user = app()->make(UserMutator::class)->toDTO($entity->user());

        return $dto;
    }
}
