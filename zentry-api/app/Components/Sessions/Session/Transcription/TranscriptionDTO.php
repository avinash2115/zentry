<?php

namespace App\Components\Sessions\Session\Transcription;

use App\Components\Sessions\Session\Transcription\Mutators\DTO\Mutator;
use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use Illuminate\Support\Collection;

/**
 * Class TranscriptionDTO
 *
 * @package App\Components\Sessions\Session\Transcription
 */
class TranscriptionDTO implements PresenterContract, LinksContract
{
    use PresenterTrait;
    use NestedLinksTrait;

    public const ROUTE_NAME_SHOW = 'sessions.transcription.show';

    /**
     * @var string
     */
    public string $word;

    /**
     * @var float
     */
    public float $startedTime;

    /**
     * @var float
     */
    public float $endedTime;

    /**
     * @var string
     */
    public string $_type = Mutator::TYPE;

    /**
     * @var string
     */
    public string $route = self::ROUTE_NAME_SHOW;

    /**
     * @var string
     */
    public string $routeParameterName = 'transcription';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'word' => $this->word,
                'started_time' => $this->startedTime,
                'ended_time' => $this->endedTime,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'word' => $this->word,
            'startedTime' => $this->startedTime,
            'endedTime' => $this->endedTime,
        ];
    }
}
