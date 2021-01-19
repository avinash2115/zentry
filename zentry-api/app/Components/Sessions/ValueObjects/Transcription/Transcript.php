<?php

namespace App\Components\Sessions\ValueObjects\Transcription;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Class Transcript
 *
 * @package App\Components\Sessions\ValueObjects\Transcription
 */
final class Transcript implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var Collection
     */
    private Collection $words;

    /**
     * @var Collection
     */
    private Collection $phrases;

    /**
     * @var string
     */
    private string $_type = 'transcripts';

    /**
     * @param Collection $words
     *
     * @throws RuntimeException
     */
    public function __construct(Collection $words)
    {
        $this->id = IdentityGenerator::next();
        $this->disableLinks();
        $this->setWords($words);
        $this->setPhrases();
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
     * @return Transcript
     */
    private function setWords(Collection $words): Transcript
    {
        $this->words = $words->sortBy(fn(Word $word) => $word->startedAt());

        return $this;
    }

    /**
     * @return Collection
     */
    public function phrases(): Collection
    {
        return $this->phrases;
    }

    /**
     * @return Transcript
     * @throws RuntimeException
     */
    private function setPhrases(): Transcript
    {
        $this->phrases = $this->words()->reduce(
            function (Collection $result, Word $word) {
                $lastSpeakerGroup = $result->last();

                if (!$lastSpeakerGroup instanceof Collection) {
                    $lastSpeakerGroup = collect();

                    $result->push($lastSpeakerGroup);
                }

                $lastWord = $lastSpeakerGroup->last();

                if (!$lastWord instanceof Word) {
                    $lastSpeakerGroup->push($word);
                } else {
                    if ($lastWord->speakerTag() === $word->speakerTag()) {
                        $lastSpeakerGroup->push($word);
                    } else {
                        $lastSpeakerGroup = collect();
                        $lastSpeakerGroup->push($word);
                        $result->push($lastSpeakerGroup);
                    }
                }

                return $result;
            },
            collect()
        )->map(
            function (Collection $speakerGroup) {
                $word = $speakerGroup->first();

                if (!$word instanceof Word) {
                    throw new RuntimeException('Cannot stat first word, something wrong');
                }

                return new Phrase($word->speakerTag(), $speakerGroup);
            }
        );

        return $this;
    }

    /**
     * @return string
     */
    public function transcript(): string
    {
        return $this->phrases()->map(fn(Phrase $phrase) => $phrase->phrase())->implode("\n\n");
    }

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'transcript' => $this->transcript(),
            ]
        );
    }
}
