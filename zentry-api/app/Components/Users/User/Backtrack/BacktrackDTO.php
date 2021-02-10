<?php

namespace App\Components\Users\User\Backtrack;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\User\Backtrack\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class BacktrackDTO
 *
 * @package App\Components\Users\User\Backtrack
 */
class BacktrackDTO implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var int
     */
    public int $backward;

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
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'backward' => $this->backward,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'backward' => $this->backward,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
            ]
        );
    }
}
