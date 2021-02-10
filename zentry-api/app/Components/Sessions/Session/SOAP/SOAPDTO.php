<?php

namespace App\Components\Sessions\Session\SOAP;

use App\Components\Sessions\Session\SOAP\Mutators\DTO\Mutator;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Sessions\Session\Poi\Participant\ParticipantDTO as PoiParticipantDTO;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Users\Participant\Goal\GoalDTO;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\ParticipantDTO;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use Illuminate\Support\Collection;

/**
 * Class SOAPDTO
 *
 * @package App\Components\Sessions\Session\SOAP
 */
class SOAPDTO implements PresenterContract, RelationshipsContract
{
    use PresenterTrait;

    public const ROUTE_NAME_SHOW = 'sessions.soaps.show';

    /**
     * @var bool
     */
    public bool $present;

    /**
     * @var string
     */
    public string $rate;

    /**
     * @var string
     */
    public string $activity;

    /**
     * @var string
     */
    public string $note;

    /**
     * @var string
     */
    public string $plan;

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var string
     */
    public string $updatedAt;

    /**
     * @var ParticipantDTO
     */
    public ParticipantDTO $participant;

    /**
     * @var GoalDTO | null
     */
    public ?GoalDTO $goal;

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
    public string $routeParameterName = 'soapId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'present' => $this->present,
                'rate' => $this->rate,
                'activity' => $this->activity,
                'note' => $this->note,
                'plan' => $this->plan,
                'updated_at' => $this->updatedAt,
                'created_at' => $this->createdAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        $relationships = collect();

        $relationships->put('participant', $this->participant);

        if ($this->goal instanceof GoalDTO) {
            $relationships->put('goal', $this->goal);
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
