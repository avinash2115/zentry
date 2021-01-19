<?php

namespace App\Components\Users\User;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\LinkParameters;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\User\Backtrack\BacktrackDTO;
use App\Components\Users\User\Mutators\DTO\Mutator;
use App\Components\Users\User\Poi\PoiDTO;
use App\Components\Users\User\Profile\ProfileDTO;
use Illuminate\Support\Collection;

/**
 * Class UserDTO
 *
 * @package App\Convention\Auth\User
 */
class UserDTO implements PresenterContract, LinksContract, RelationshipsContract
{
    use PresenterTrait;
    use NestedLinksTrait;

    public const ROUTE_NAME_SHOW = 'users.show';

    /**
     * @var string
     */
    public string $email;

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var string
     */
    public string $updatedAt;

    /**
     * @var string|null
     */
    public ?string $archivedAt;

    /**
     * @var ProfileDTO
     */
    public ProfileDTO $profileDTO;

    /**
     * @var PoiDTO
     */
    public PoiDTO $poiDTO;

    /**
     * @var BacktrackDTO
     */
    public BacktrackDTO $backtrackDTO;

    /**
     * @var Collection
     */
    public Collection $storages;

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
    public string $routeParameterName = 'userId';

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'archivedAt' => $this->archivedAt,
            'profile' => $this->profileDTO->toArray(),
            'poi' => $this->poiDTO->toArray(),
            'backtrack' => $this->backtrackDTO->toArray(),

        ];
    }

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'email' => $this->email,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
                'archived_at' => $this->archivedAt,
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
                'poi' => $this->poiDTO,
                'backtrack' => $this->backtrackDTO,
                'profile' => $this->profileDTO,
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
}
