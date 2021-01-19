<?php

namespace App\Components\Users\User\Poi;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\User\Poi\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class PoiDTO
 *
 * @package App\Components\Users\User\Poi
 */
class PoiDTO implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var int
     */
    public int $backward;

    /**
     * @var int
     */
    public int $forward;

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
            'forward' => $this->forward,
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
                'forward' => $this->forward,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
            ]
        );
    }
}
