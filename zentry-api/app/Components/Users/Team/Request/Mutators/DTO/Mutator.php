<?php

namespace App\Components\Users\Team\Request\Mutators\DTO;

use App\Components\Users\Team\Request\RequestDTO;
use App\Components\Users\Team\Request\RequestReadonlyContract;
use App\Components\Users\User\Mutators\DTO\Traits\MutatorTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Mutator
 */
final class Mutator
{
    use MutatorTrait;

    public const TYPE = 'users_teams_requests';

    /**
     * @param RequestReadonlyContract $entity
     *
     * @return RequestDTO
     * @throws BindingResolutionException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function toDTO(RequestReadonlyContract $entity): RequestDTO
    {
        $dto = new RequestDTO();
        $dto->id = $entity->identity()->toString();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());

        $this->userMutator__()->simplifiedMutation();

        $dto->user = $this->userMutator__()->toDTO($entity->user());

        return $dto;
    }
}
