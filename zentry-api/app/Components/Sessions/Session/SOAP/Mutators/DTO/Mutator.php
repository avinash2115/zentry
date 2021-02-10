<?php

namespace App\Components\Sessions\Session\SOAP\Mutators\DTO;

use App\Components\Sessions\Session\SOAP\SOAPDTO;
use App\Components\Sessions\Session\SOAP\SOAPReadonlyContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\Mutators\DTO\Traits\MutatorTrait as ParticipantGoalMutatorTrait;
use App\Components\Users\Participant\Mutators\DTO\Traits\MutatorTrait as ParticipantMutatorTrait;
use App\Convention\DTO\Mutators\SimplifiedDTOContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Mutator
 *
 * @package App\Components\Sessions\Session\SOAP\Mutators\DTO
 */
final class Mutator implements SimplifiedDTOContract
{
    use SimplifiedDTOTrait;
    use ParticipantMutatorTrait;
    use ParticipantGoalMutatorTrait;

    public const TYPE = 'sessions_soaps';

    /**
     * @param SOAPReadonlyContract $entity
     *
     * @return SOAPDTO
     * @throws BindingResolutionException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function toDTO(SOAPReadonlyContract $entity): SOAPDTO
    {
        $dto = new SOAPDTO();
        $dto->id = $entity->identity()->toString();
        $dto->present = $entity->isPresent();
        $dto->rate = $entity->rate();
        $dto->activity = $entity->activity();
        $dto->note = $entity->note();
        $dto->plan = $entity->plan();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $this->participantMutator__()->simplifiedMutation();
        $this->goalMutator__()->simplifiedMutation();

        $dto->participant = $this->participantMutator__()->toDTO($entity->participant());
        $dto->goal = null;

        if ($entity->goal() instanceof GoalReadonlyContract) {
            $dto->goal = $this->goalMutator__()->toDTO($entity->goal());
            $dto->goal->disableLinks();
        }

        return $dto;
    }
}
