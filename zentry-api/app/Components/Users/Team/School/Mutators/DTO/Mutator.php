<?php

namespace App\Components\Users\Team\School\Mutators\DTO;

use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Team\School\SchoolDTO;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Participant\Mutators\DTO\Traits\MutatorTrait as ParticipantMutatorTrait;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourceMutatorTrait;
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
    use ParticipantMutatorTrait;
    use SourceMutatorTrait;

    public const TYPE = 'users_teams_schools';

    /**
     * @param SchoolReadonlyContract $entity
     *
     * @return SchoolDTO
     * @throws BindingResolutionException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function toDTO(SchoolReadonlyContract $entity): SchoolDTO
    {
        $dto = new SchoolDTO();
        $dto->id = $entity->identity()->toString();
        $dto->name = $entity->name();
        $dto->available = $entity->available();
        $dto->streetAddress = $entity->streetAddress();
        $dto->city = $entity->city();
        $dto->state = $entity->state();
        $dto->zip = $entity->zip();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $this->fill($dto, $entity);

        $dto->participants = collect();

        if (!$this->isSimplifiedMutation()) {
            $dto->participants = $entity->participants()->map(
                function (ParticipantReadonlyContract $participant) {
                    $this->participantMutator__()->simplifiedMutation();

                    return $this->participantMutator__()->toDTO($participant);
                }
            );
        }

        return $dto;
    }
}
