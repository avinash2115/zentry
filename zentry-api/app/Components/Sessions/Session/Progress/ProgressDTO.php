<?php

namespace App\Components\Sessions\Session\Progress;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Sessions\Session\Progress\Mutators\DTO\Mutator;
use App\Components\Users\Participant\Goal\GoalDTO;
use App\Components\Users\Participant\Goal\Tracker\TrackerDTO;
use App\Components\Users\Participant\ParticipantDTO;
use Illuminate\Support\Collection;

/**
 * Class ProgressDTO
 *
 * @package App\Components\Sessions\Session\Progress
 */
class ProgressDTO implements PresenterContract, RelationshipsContract
{
    use PresenterTrait;

    public const ROUTE_NAME_SHOW = 'sessions.progress.show';

    /**
     * @var string
     */
    public string $datetime;

    /**
     * @var ParticipantDTO
     */
    public ParticipantDTO $participant;

    /**
     * @var GoalDTO
     */
    public GoalDTO $goal;

    /**
     * @var TrackerDTO
     */
    public TrackerDTO $tracker;

    /**
     * @var PoiDTO|null
     */
    public ?PoiDTO $poi = null;

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
    public string $routeParameterName = 'progressId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'datetime' => $this->datetime,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        $relationships = collect(
            [
                'participant' => $this->participant,
                'goal' => $this->goal,
                'tracker' => $this->tracker,
            ]
        );

        if ($this->poi instanceof PoiDTO) {
            $relationships->put('poi', $this->poi);
        }

        return $relationships;
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return $this->nested();
    }
}
