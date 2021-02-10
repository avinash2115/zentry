<?php

namespace App\Components\Sessions\Session\Goal;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Sessions\Session\Goal\Mutators\DTO\Mutator;
use App\Components\Users\Participant\Goal\GoalDTO as ParticipantGoalDTO;
use App\Components\Users\Participant\ParticipantDTO;
use App\Components\CRM\Source\Mutators\DTO\Contracts\SourcedDTOContract;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourcedDTOTrait;
use Illuminate\Support\Collection;

/**
 * Class GoalDTO
 *
 * @package App\Components\Sessions\Session\Goal
 */
class GoalDTO implements PresenterContract, RelationshipsContract
{
    use PresenterTrait;

    /**
     * @var ParticipantGoalDTO
     */
    public ParticipantGoalDTO $goal;

    /**
     * @var ParticipantDTO
     */
    public ParticipantDTO $participant;

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
    public string $routeParameterName = 'goalId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'created_at' => $this->createdAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        return collect(
            [
                'participant' => $this->participant,
                'goal' => $this->goal,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return collect();
    }
}
