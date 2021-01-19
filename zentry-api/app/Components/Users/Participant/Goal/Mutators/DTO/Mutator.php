<?php

namespace App\Components\Users\Participant\Goal\Mutators\DTO;

use App\Components\Users\Participant\Goal\GoalDTO;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Traits\MutatorTrait;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Components\Users\Participant\IEP\Mutators\DTO\Traits\MutatorTrait as IEPMutatorTrait;
use App\Components\Users\Participant\Goal\Tracker\TrackerReadonlyContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourceMutatorTrait;
use InvalidArgumentException;

/**
 * Class Mutator
 *
 * @package App\Components\Users\Participant\Goal\Mutators\DTO
 */
final class Mutator
{
    use MutatorTrait;
    use SourceMutatorTrait;
    use SimplifiedDTOTrait;
    use IEPMutatorTrait;

    public const TYPE = 'users_participants_goals';

    /**
     * @param GoalReadonlyContract $entity
     *
     * @return GoalDTO
     * @throws InvalidArgumentException
     */
    public function toDTO(GoalReadonlyContract $entity): GoalDTO
    {
        $dto = new GoalDTO();

        $dto->id = $entity->identity()->toString();
        $dto->name = $entity->name();
        $dto->description = $entity->description();
        $dto->reached = $entity->isReached();

        $this->fill($dto, $entity);

        if (!$this->isSimplifiedMutation()) {
            if ($entity->iep() instanceof IEPReadonlyContract) {
                $dto->iep = $this->iepMutator__()->toDTO($entity->iep());
            }
        }

        $dto->trackers = $entity->trackers()->map(function(TrackerReadonlyContract $tracker) {
            $trackerDT0 = $this->trackerMutator__()->toDTO($tracker);
            $trackerDT0->disableLinks();

            return $trackerDT0;
        });

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        return $dto;
    }
}
