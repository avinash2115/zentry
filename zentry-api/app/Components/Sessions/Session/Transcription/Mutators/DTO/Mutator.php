<?php

namespace App\Components\Sessions\Session\Transcription\Mutators\DTO;

use App\Components\Sessions\Session\Transcription\TranscriptionDTO;
use App\Components\Sessions\Session\Transcription\TranscriptionReadonlyContract;

/**
 * Class Mutator
 */
final class Mutator
{
    public const TYPE = 'sessions_transcriptions';

    /**
     * @param TranscriptionReadonlyContract $entity
     *
     * @return TranscriptionDTO
     */
    public function toDTO(TranscriptionReadonlyContract $entity): TranscriptionDTO
    {
        $dto = new TranscriptionDTO();

        $dto->id = $entity->identity()->toString();
        $dto->word = $entity->word();
        $dto->startedTime = $entity->startedAt();
        $dto->endedTime = $entity->endedAt();

        return $dto;
    }
}
