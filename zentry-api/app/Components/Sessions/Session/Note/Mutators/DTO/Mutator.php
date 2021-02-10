<?php

namespace App\Components\Sessions\Session\Note\Mutators\DTO;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Components\Sessions\Session\Note\NoteDTO;
use App\Components\Sessions\Session\Note\NoteReadonlyContract;
use App\Components\Sessions\Session\Poi\Participant\Mutators\DTO\Traits\MutatorTrait as PoiParticipantMutatorTrait;
use App\Components\Sessions\Session\Poi\Mutators\DTO\Traits\MutatorTrait as PoiMutatorTrait;
use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract as PoiParticipantReadonlyContract;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\DTO\Mutators\SimplifiedDTOContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;
use App\Components\Users\Participant\Mutators\DTO\Traits\MutatorTrait as ParticipantMutatorTrait;

/**
 * Class Mutator
 *
 * @package App\Components\Sessions\Session\Note\Mutators\DTO
 */
final class Mutator implements SimplifiedDTOContract
{
    use ParticipantMutatorTrait;
    use PoiMutatorTrait;
    use PoiParticipantMutatorTrait;
    use SimplifiedDTOTrait;
    use FileServiceTrait;

    public const TYPE = 'sessions_notes';
    public const TYPE_PROGRESS = 'sessions_notes_upload_progress';

    /**
     * @param NoteReadonlyContract $entity
     *
     * @return NoteDTO
     * @throws BindingResolutionException|RuntimeException
     * @throws InvalidArgumentException
     */
    public function toDTO(NoteReadonlyContract $entity): NoteDTO
    {
        $dto = new NoteDTO();
        $dto->id = $entity->identity()->toString();
        $dto->text = $entity->text();

        if ($entity->url() !== null) {
            $dto->url = $this->fileService__()->temporaryUrl($entity->url(), $dto->id, 600)->url();
        }

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $this->participantMutator__()->simplifiedMutation();
        $this->poiMutator__()->simplifiedMutation();
        $this->poiParticipantMutator__()->simplifiedMutation();

        if ($entity->participant() instanceof ParticipantReadonlyContract) {
            $dto->participant = $this->participantMutator__()->toDTO($entity->participant());
        }

        if ($entity->poi() instanceof PoiReadonlyContract) {
            $dto->poi = $this->poiMutator__()->toDTO($entity->poi());
        }

        if ($entity->poiParticipant() instanceof PoiParticipantReadonlyContract) {
            $dto->poiParticipant = $this->poiParticipantMutator__()->toDTO($entity->poiParticipant());
        }

        return $dto;
    }
}
