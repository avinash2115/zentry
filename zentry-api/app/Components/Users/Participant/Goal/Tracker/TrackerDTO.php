<?php

namespace App\Components\Users\Participant\Goal\Tracker;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class TrackerDTO
 *
 * @package App\Components\Users\Participant\Goal\Tracker
 */
class TrackerDTO implements PresenterContract, LinksContract, RelationshipsContract
{
    use PresenterTrait;
    use NestedLinksTrait;

    public const ROUTE_NAME_SHOW = 'users.participants.goals.trackers.show';

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $type;

    /**
     * @var string
     */
    public string $icon;

    /**
     * @var string
     */
    public string $color;

    /**
     * @var Collection
     */
    public Collection $sessions;

    /**
     * @var string
     */
    public string $createdAt;

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
    public string $routeParameterName = 'trackerId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'name' => $this->name,
                'type' => $this->type,
                'icon' => $this->icon,
                'color' => $this->color,
                'created_at' => $this->createdAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        return collect(['sessions' => $this->sessions]);
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return collect();
    }
}
