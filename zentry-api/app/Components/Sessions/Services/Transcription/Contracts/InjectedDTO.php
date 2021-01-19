<?php

namespace App\Components\Sessions\Services\Transcription\Contracts;

use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Components\Sessions\ValueObjects\Transcription\Transcript;
use Illuminate\Support\Collection;

/**
 * Class InjectedDTO
 *
 * @package App\Components\Sessions\Services\Transcription\Contracts
 */
abstract class InjectedDTO implements PresenterContract, RelationshipsContract
{
    /**
     * @var Transcript|null
     */
    public ?Transcript $transcript = null;

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        $nested = collect();

        if ($this->transcript instanceof Transcript) {
            $nested->put('transcript', $this->transcript);
        }

        return $nested;
    }
}
