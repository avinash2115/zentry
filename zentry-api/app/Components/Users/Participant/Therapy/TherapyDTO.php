<?php

namespace App\Components\Users\Participant\Therapy;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\LinkParameters;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\Participant\Therapy\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class TherapyDTO
 *
 * @package App\Components\Users\Participant\Therapy
 */
class TherapyDTO implements PresenterContract, LinksContract
{
    use PresenterTrait;

    public const ROUTE_NAME_SHOW = 'users.participants.therapy.show';

    /**
     * @var string
     */
    public string $diagnosis;

    /**
     * @var string
     */
    public string $frequency;

    /**
     * @var string
     */
    public string $eligibility;

    /**
     * @var int
     */
    public int $sessionsAmountPlanned;

    /**
     * @var int
     */
    public int $treatmentAmountPlanned;

    /**
     * @var string
     */
    public string $notes;

    /**
     * @var string
     */
    public string $privateNotes;

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
    public string $routeParameterName = 'participantId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'diagnosis' => $this->diagnosis,
                'frequency' => $this->frequency,
                'eligibility' => $this->eligibility,
                'sessions_amount_planned' => $this->sessionsAmountPlanned,
                'treatment_amount_planned' => $this->treatmentAmountPlanned,
                'notes' => $this->notes,
                'private_notes' => $this->privateNotes,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
            ]
        );
    }


    /**
     * @return Collection
     */
    public function routeParameters(): Collection
    {
        return collect();
    }

    /**
     * @inheritDoc
     */
    public function data(LinkParameters $linkParameters): Collection
    {
        return collect(
            [
                'self' => route(
                    $this->route(),
                    [$this->routeParameterName => $linkParameters->stack()->get($this->routeParameterName)]
                ),
            ]
        );
    }
}
