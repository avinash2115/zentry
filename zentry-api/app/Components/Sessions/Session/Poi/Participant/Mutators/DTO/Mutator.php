<?php

namespace App\Components\Sessions\Session\Poi\Participant\Mutators\DTO;

use App\Components\Sessions\Session\Poi\Participant\ParticipantDTO;
use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract;
use App\Components\Users\Participant\Mutators\DTO\Traits\MutatorTrait as ParticipantMutatorTrait;
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
    use ParticipantMutatorTrait;
    use SimplifiedDTOTrait;

    public const TYPE = 'sessions_pois_participants';

    /**
     * @param ParticipantReadonlyContract $entity
     *
     * @return ParticipantDTO
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
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

        $this->participantMutator__()->simplifiedMutation();

        $dto->raw = $this->participantMutator__()->toDTO($entity->raw());

        $dto->startedAt = dateTimeFormatted($entity->startedAt());
        $dto->endedAt = dateTimeFormatted($entity->endedAt());

        return $dto;
    }
}
