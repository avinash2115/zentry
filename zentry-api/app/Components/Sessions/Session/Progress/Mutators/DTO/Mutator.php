<?php

namespace App\Components\Sessions\Session\Progress\Mutators\DTO;

use App\Components\Sessions\Session\Progress\ProgressDTO;
use App\Components\Sessions\Session\Progress\ProgressReadonlyContract;
use App\Components\Sessions\Session\Stream\Mutators\DTO\Mutator as StreamMutator;
use App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator as PoiMutator;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Users\Participant\Goal\Mutators\DTO\Traits\MutatorTrait as GoalMutatorTrait;
use App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Traits\MutatorTrait as TrackerMutatorTrait;
use App\Convention\DTO\Mutators\SimplifiedDTOContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;
use App\Components\Users\Participant\Mutators\DTO\Traits\MutatorTrait as ParticipantMutatorTrait;

/**
 * Class Mutator
 */
final class Mutator implements SimplifiedDTOContract
{
    use GoalMutatorTrait;
    use ParticipantMutatorTrait;
    use TrackerMutatorTrait;
    use SimplifiedDTOTrait;

    public const TYPE = 'sessions_progress';

    /**
     * @var StreamMutator
     */
    private StreamMutator $streamMutator;

    /**
     * @var PoiMutator
     */
    private PoiMutator $poiMutator;

    /**
     * Mutator constructor.
     *
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->poiMutator = app()->make(PoiMutator::class);
    }

    /**
     * @param ProgressReadonlyContract $entity
     *
     * @return ProgressDTO
     * @throws BindingResolutionException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function toDTO(ProgressReadonlyContract $entity): ProgressDTO
    {
        $dto = new ProgressDTO();
        $dto->id = $entity->identity()->toString();

        $this->participantMutator__()->simplifiedMutation();
        $this->goalMutator__()->simplifiedMutation();
        $this->trackerMutator__()->simplifiedMutation();

        $dto->datetime = dateTimeFormatted($entity->datetime());
        $dto->participant = $this->participantMutator__()->toDTO($entity->participant());
        $dto->goal = $this->goalMutator__()->toDTO($entity->goal());
        $dto->tracker = $this->trackerMutator__()->toDTO($entity->tracker());

        $dto->goal->disableLinks();
        $dto->tracker->disableLinks();

        if ($entity->poi() instanceof PoiReadonlyContract) {
            $dto->poi = $this->poiMutator->toDTO($entity->poi());
        }

        return $dto;
    }
}
