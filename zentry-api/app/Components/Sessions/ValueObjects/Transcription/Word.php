<?php

namespace App\Components\Sessions\ValueObjects\Transcription;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Sessions\Session\Transcription\TranscriptionReadonlyContract;
use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Collection;

/**
 * Class Word
 *
 * @package App\Components\Sessions\ValueObjects\Transcription
 */
final class Word implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    private string $word;

    /**
     * @var float
     */
    private float $startedAt;

    /**
     * @var float
     */
    private float $endedAt;

    /**
     * @var int
     */
    private int $speakerTag;

    /**
     * @var string
     */
    private string $_type = 'transcripts_words';

    /**
     * @param TranscriptionReadonlyContract $entity
     */
    public function __construct(TranscriptionReadonlyContract $entity)
    {
        $this->id = IdentityGenerator::next();
        $this->disableLinks();

        $this->word = $entity->word();
        $this->startedAt = $entity->startedAt();
        $this->endedAt = $entity->endedAt();
        $this->speakerTag = $entity->speakerTag();
    }

    /**
     * @return string
     */
    public function word(): string
    {
        return $this->word;
    }

    /**
     * @return float
     */
    public function startedAt(): float
    {
        return $this->startedAt;
    }

    /**
     * @return float
     */
    public function endedAt(): float
    {
        return $this->endedAt;
    }

    /**
     * @return int
     */
    public function speakerTag(): int
    {
        return $this->speakerTag;
    }

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'word' => $this->word(),
                'started_at' => $this->startedAt(),
                'ended_at' => $this->endedAt(),
            ]
        );
    }
}
