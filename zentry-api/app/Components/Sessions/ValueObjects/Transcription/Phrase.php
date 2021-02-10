<?php

namespace App\Components\Sessions\ValueObjects\Transcription;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class Phrase
 *
 * @package App\Components\Sessions\ValueObjects\Transcription
 */
final class Phrase implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var int
     */
    private int $speaker;

    /**
     * @var Collection
     */
    private Collection $words;

    /**
     * @var string
     */
    private string $_type = 'transcripts_phrases';

    /**
     * @param int        $speaker
     * @param Collection $words
     *
     * @throws InvalidArgumentException
     */
    public function __construct(int $speaker, Collection $words)
    {
        $this->id = IdentityGenerator::next();
        $this->disableLinks();
        $this->speaker = $speaker;
        $this->setWords($words);
    }

    /**
     * @return int
     */
    public function speaker(): int
    {
        return $this->speaker;
    }

    /**
     * @return Collection
     */
    public function words(): Collection
    {
        return $this->words;
    }

    /**
     * @param Collection $words
     *
     * @return Phrase
     * @throws InvalidArgumentException
     */
    private function setWords(Collection $words): Phrase
    {
        if ($words->some(fn(Word $word) => $word->speakerTag() !== $this->speaker())) {
            throw new InvalidArgumentException('Words should have the same speaker');
        }

        $this->words = $words->sortBy(fn(Word $word) => $word->startedAt());

        return $this;
    }

    /**
     * @return string
     */
    public function phrase(): string
    {
        return $this->words()->map(fn(Word $word) => $word->word())->implode(' ');
    }

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'phrase' => $this->phrase(),
            ]
        );
    }
}
