<?php

namespace App\Components\Users\Participant\Goal;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\Participant\Goal\Mutators\DTO\Mutator;
use App\Components\CRM\Source\Mutators\DTO\Contracts\SourcedDTOContract;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourcedDTOTrait;
use App\Components\Users\Participant\IEP\IEPDTO;
use Illuminate\Support\Collection;

/**
 * Class GoalDTO
 *
 * @package App\Components\Users\Participant\Goal
 */
class GoalDTO implements PresenterContract, LinksContract, SourcedDTOContract, RelationshipsContract
{
    use PresenterTrait;
    use NestedLinksTrait;
    use SourcedDTOTrait;

    public const ROUTE_NAME_SHOW = 'users.participants.goals.show';

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $description;

    /**
     * @var bool
     */
    public bool $reached;

    /**
     * @var Collection
     */
    public Collection $trackers;

    /**
     * @var IEPDTO|null
     */
    public ?IEPDTO $iep = null;

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var string
     */
    public string $updatedAt;

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
    public string $routeParameterName = 'goalId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'name' => $this->name,
                'description' => $this->description,
                'reached' => $this->reached,
                'imported' => $this->imported,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
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
                'trackers' => $this->trackers,
                'sources' => $this->sources,
            ]
        );

        if ($this->iep instanceof IEPDTO) {
            $relationships->put('iep', $this->iep);
        }

        return $relationships;
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return collect();
    }
}
