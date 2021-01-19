<?php

namespace App\Components\Users\User\Storage;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\User\Storage\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class StorageDTO
 *
 * @package App\Components\Users\User\Storage
 */
class StorageDTO implements PresenterContract, LinksContract
{
    use PresenterTrait;
    use NestedLinksTrait;

    public const ROUTE_NAME_SHOW = 'users.storages.show';

    /**
     * @var string
     */
    public string $driver;

    /**
     * @var string
     */
    public string $name;

    /**
     * @var bool
     */
    public bool $enabled;

    /**
     * @var bool
     */
    public bool $available;

    /**
     * @var int
     */
    public int $used;

    /**
     * @var int
     */
    public int $capacity;

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
     * @var string
     */
    public string $routeParameterName = 'storageId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'driver' => $this->driver,
                'name' => $this->name,
                'enabled' => $this->enabled,
                'available' => $this->available,
                'used' => $this->used,
                'capacity' => $this->capacity,
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
            'driver' => $this->driver,
            'name' => $this->name,
            'enabled' => $this->enabled,
            'available' => $this->available,
            'used' => $this->used,
            'capacity' => $this->capacity,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
