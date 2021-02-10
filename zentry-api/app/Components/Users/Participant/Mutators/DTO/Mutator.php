<?php

namespace App\Components\Users\Participant\Mutators\DTO;

use App\Components\CRM\Source\Mutators\DTO\Traits\SourceMutatorTrait;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\Goal\Mutators\DTO\Traits\MutatorTrait as GoalMutatorTrait;
use App\Components\Users\Participant\IEP\Mutators\DTO\Traits\MutatorTrait as IEPMutatorTrait;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Components\Users\Participant\ParticipantDTO;
use App\Components\Users\Participant\Therapy\Mutators\DTO\Traits\MutatorTrait as TherapyMutatorTrait;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Team\Mutators\DTO\Traits\MutatorTrait as TeamMutatorTrait;
use App\Components\Users\Team\School\Mutators\DTO\Traits\MutatorTrait as SchoolMutatorTrait;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\User\Mutators\DTO\Traits\MutatorTrait as UserMutatorTrait;
use App\Convention\DTO\Mutators\SimplifiedDTOContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Mutator
 */
final class Mutator implements SimplifiedDTOContract
{
    use SimplifiedDTOTrait;
    use TherapyMutatorTrait;
    use GoalMutatorTrait;
    use IEPMutatorTrait;
    use UserMutatorTrait;
    use TeamMutatorTrait;
    use SchoolMutatorTrait;
    use SourceMutatorTrait;

    public const TYPE = 'users_participants';

    /**
     * @param ParticipantReadonlyContract $entity
     *
     * @return ParticipantDTO
     * @throws BindingResolutionException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function toDTO(ParticipantReadonlyContract $entity): ParticipantDTO
    {
        $dto = new ParticipantDTO();
        $dto->id = $entity->identity()->toString();
        $dto->email = $entity->email();
        $dto->firstName = $entity->firstName();
        $dto->lastName = $entity->lastName();
        $dto->phoneCode = $entity->phoneCode();
        $dto->phoneNumber = $entity->phoneNumber();
        $dto->avatar = $entity->avatar();
        $dto->gender = $entity->gender();
        $dto->dob = dateTimeFormatted($entity->dob());
        $dto->parentEmail = $entity->parentEmail();
        $dto->parentPhoneNumber = $entity->parentPhoneNumber();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $this->userMutator__()->simplifiedMutation();
        $dto->user = $this->userMutator__()->toDTO($entity->user());
        $dto->therapy = $this->therapyMutator__()->toDTO($entity->therapy());

        $dto->goals = $entity->goals()->map(function(GoalReadonlyContract $goal) {
            return $this->goalMutator__()->toDTO($goal);
        });

        $dto->ieps = $entity->ieps()->map(function(IEPReadonlyContract $entity) {
            return $this->iepMutator__()->toDTO($entity);
        });

        $this->fill($dto, $entity);

        if (!$this->isSimplifiedMutation()) {
            if ($entity->team() instanceof TeamReadonlyContract) {
                $this->teamMutator__()->simplifiedMutation();

                $dto->team = $this->teamMutator__()->toDTO($entity->team());
                $dto->team->disableLinks();

                if ($entity->school() instanceof SchoolReadonlyContract) {
                    $this->schoolMutator__()->simplifiedMutation();

                    $dto->school = $this->schoolMutator__()->toDTO($entity->school());
                    $dto->school->disableLinks();
                }
            }
        }

        return $dto;
    }
}
