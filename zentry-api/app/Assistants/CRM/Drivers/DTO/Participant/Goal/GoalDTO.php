<?php

namespace App\Assistants\CRM\Drivers\DTO\Participant\Goal;

use App\Assistants\Transformers\JsonApi\Traits\IdTrait;

/**
 * Class GoalDTO
 *
 * @package App\Assistants\CRM\Drivers\DTO\Participant\Goal
 */
class GoalDTO
{
    use IdTrait;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var array
     */
    public array $meta;

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'meta' => $this->meta,
        ];
    }
}
