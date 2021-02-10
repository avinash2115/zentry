<?php

namespace App\Components\Users\Login\Token\Mutators\DTO;

use App\Components\Users\Login\Token\TokenDTO;
use App\Components\Users\Login\Token\TokenReadonlyContract;

/**
 * Class Mutator
 */
final class Mutator
{
    public const TYPE = 'login_tokens';

    /**
     * @param TokenReadonlyContract $entity
     *
     * @return TokenDTO
     */
    public function toDTO(TokenReadonlyContract $entity): TokenDTO
    {
        $dto = new TokenDTO();
        $dto->id = $entity->identity()->toString();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());

        return $dto;
    }
}
