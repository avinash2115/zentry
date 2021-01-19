<?php

namespace App\Components\Sessions\Session\Poi\Mutators\DTO;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Components\Sessions\Session\Poi\Participant\Mutators\DTO\Traits\MutatorTrait as PoiParticipantMutatorTrait;
use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Convention\DTO\Mutators\SimplifiedDTOContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use RuntimeException;

/**
 * Class Mutator
 */
final class Mutator implements SimplifiedDTOContract
{
    use FileServiceTrait;
    use SimplifiedDTOTrait;
    use PoiParticipantMutatorTrait;

    public const TYPE = 'sessions_pois';

    /**
     * @param PoiReadonlyContract $entity
     *
     * @return PoiDTO
     * @throws BindingResolutionException
     * @throws RuntimeException
     */
    public function toDTO(PoiReadonlyContract $entity): PoiDTO
    {
        $dto = new PoiDTO();
        $dto->id = $entity->identity()->toString();
        $dto->type = $entity->type();
        $dto->name = $entity->name();
        $dto->tags = $entity->tags();
        $dto->thumbnailURL = $entity->thumbnail() === null ? null : $this->fileService__()->temporaryUrl(
            $entity->thumbnail(),
            $dto->id,
            600
        )->url();

        $dto->duration = $entity->duration();
        $dto->participants = $entity->participants()->map(
            function (ParticipantReadonlyContract $participant) {
                return $this->poiParticipantMutator__()->toDTO($participant);
            }
        );

        $dto->startedAt = dateTimeFormatted($entity->startedAt());
        $dto->endedAt = dateTimeFormatted($entity->endedAt());
        $dto->createdAt = dateTimeFormatted($entity->createdAt());

        return $dto;
    }
}
