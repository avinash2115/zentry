<?php

namespace App\Components\Share\Shared;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\BasicLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Share\Shared\Mutators\DTO\Mutator;
use App\Components\Share\ValueObjects\Payload;
use Illuminate\Support\Collection;

/**
 * Class SharedDTO
 *
 * @package App\Components\Share\Shared
 */
class SharedDTO implements PresenterContract
{
    use PresenterTrait;
    use BasicLinksTrait;

    public const ROUTE_NAME_SHOW = 'shared.show';

    /**
     * @var string
     */
    public string $type;

    /**
     * @var Payload
     */
    public Payload $payload;

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
    protected string $_type = Mutator::TYPE;

    /**
     * @var string
     */
    public string $route = self::ROUTE_NAME_SHOW;

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'type' => $this->type,
                'payload' => $this->payload->toArray(),
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'payload' => $this->payload->toArray(),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
