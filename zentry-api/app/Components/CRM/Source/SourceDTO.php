<?php

namespace App\Components\CRM\Source;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\CRM\Source\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class SourceDTO
 *
 * @package App\Components\CRM\Source
 */
class SourceDTO implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    public string $id;

    /**
     * @var mixed
     */
    public $owner;

    /**
     * @var string
     */
    public string $sourceId;

    /**
     * @var string
     */
    public string $direction;

    /**
     * @var string|null
     */
    public ?string $createdAt;

    /**
     * @var string|null
     */
    public ?string $updatedAt;

    /**
     * @var string
     */
    public string $_type = Mutator::TYPE;

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect($this->toArray());
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'owner' => $this->owner,
            'sourceId' => $this->sourceId,
            'direction' => $this->direction,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
