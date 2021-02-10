<?php

namespace App\Components\Users\Team\Mutators\DTO;

use App\Components\Users\Participant\Mutators\DTO\Traits\MutatorTrait as ParticipantMutatorTrait;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Team\Request\Mutators\DTO\Traits\MutatorTrait as RequestMutatorTrait;
use App\Components\Users\Team\Request\RequestReadonlyContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\School\Mutators\DTO\Traits\MutatorTrait as SchoolMutatorTrait;
use App\Components\Users\Team\TeamDTO;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourceMutatorTrait;
use App\Components\Users\User\Mutators\DTO\Traits\MutatorTrait as UserMutatorTrait;
use App\Components\Users\User\UserReadonlyContract;
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
    use UserMutatorTrait;
    use RequestMutatorTrait;
    use ParticipantMutatorTrait;
    use SchoolMutatorTrait;
    use SourceMutatorTrait;

    public const TYPE = 'users_teams';

    /**
     * @param TeamReadonlyContract $entity
     *
     * @return TeamDTO
     * @throws BindingResolutionException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function toDTO(TeamReadonlyContract $entity): TeamDTO
    {
        $dto = new TeamDTO();
        $dto->id = $entity->identity()->toString();
        $dto->name = $entity->name();
        $dto->description = $entity->description();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $this->userMutator__()->simplifiedMutation();

        $dto->owner = $this->userMutator__()->toDTO($entity->owner());

        $dto->members = $entity->members()->map(function(UserReadonlyContract $user) {
            return $this->userMutator__()->toDTO($user);
        });

        $dto->requests = $entity->requests()->map(function(RequestReadonlyContract $request) {
            return $this->teamRequestMutator__()->toDTO($request);
        });

        $dto->schools = collect();
        $dto->participants = collect();

        $this->fill($dto, $entity);

        if (!$this->isSimplifiedMutation()) {
            $dto->schools = $entity->schools()->map(function(SchoolReadonlyContract $school) {
                return $this->schoolMutator__()->toDTO($school);
            });

            $this->participantMutator__()->simplifiedMutation();

            $dto->participants = $entity->participants()->map(function(ParticipantReadonlyContract $participant) {
                return $this->participantMutator__()->toDTO($participant);
            });
        }

        return $dto;
    }
}
