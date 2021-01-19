<?php

namespace App\Components\Users\Team\Request;

use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Components\Users\Team\Request\Mutators\DTO\Mutator;
use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\User\UserDTO;
use Illuminate\Support\Collection;

/**
 * Class RequestDTO
 *
 * @package App\Components\Users\Team\Request
 */
class RequestDTO implements PresenterContract, LinksContract, RelationshipsContract
{
    use PresenterTrait;
    use NestedLinksTrait;

    public const ROUTE_NAME_SHOW = 'users.teams.requests.show';

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var UserDTO
     */
    public UserDTO $user;

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
    public string $routeParameterName = 'requestId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'created_at' => $this->createdAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'createdAt' => $this->createdAt,
        ];
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
        return collect();
    }
}
