<?php

namespace App\Components\Users\Device;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\Device\Mutators\DTO\Mutator;
use App\Components\Users\User\UserDTO;
use Illuminate\Support\Collection;

/**
 * Class DeviceDTO
 *
 * @package App\Components\Users\Device
 */
class DeviceDTO implements PresenterContract, LinksContract, RelationshipsContract
{
    use PresenterTrait;
    use NestedLinksTrait;

    public const ROUTE_NAME_SHOW = 'devices.show';

    /**
     * @var string
     */
    public string $type;

    /**
     * @var string
     */
    public string $model;

    /**
     * @var string
     */
    public string $reference;

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var string
     */
    public string $updatedAt;

    /**
     * @var UserDTO
     */
    public UserDTO $user;

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
    public string $routeParameterName = 'deviceId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'type' => $this->type,
                'model' => $this->model,
                'reference' => $this->reference,
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
        return collect(
            [
                'user' => $this->user,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return $this->nested();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'model' => $this->model,
            'reference' => $this->reference,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'user' => $this->user->toArray(),
        ];
    }
}
