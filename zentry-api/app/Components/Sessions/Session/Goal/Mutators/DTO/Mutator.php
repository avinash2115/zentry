<?php

namespace App\Components\Sessions\Session\Goal\Mutators\DTO;

use App\Components\Sessions\Session\Goal\GoalDTO;
use App\Components\Sessions\Session\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\Mutators\DTO\Traits\MutatorTrait;
use App\Components\Users\Participant\Mutators\DTO\Traits\MutatorTrait as ParticipantMutatorTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;

/**
 * Class Mutator
 *
 * @package App\Components\Sessions\Session\Goal\Mutators\DTO
 */
final class Mutator
{
    use MutatorTrait;
    use ParticipantMutatorTrait;

    public const TYPE = 'session_participants_goals';

    /**
     * @param GoalReadonlyContract $entity
     *
     * @return GoalDTO
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     */
    public function toDTO(GoalReadonlyContract $entity): GoalDTO
    {
        $dto = new GoalDTO();
        $this->goalMutator__()->simplifiedMutation();
        $this->participantMutator__()->simplifiedMutation();

        $dto->id = $entity->identity()->toString();
        $dto->goal = $this->goalMutator__()->toDTO($entity->goal());
        $dto->participant = $this->participantMutator__()->toDTO($entity->participant());

        $dto->goal->disableLinks();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());

        return $dto;
    }
}
