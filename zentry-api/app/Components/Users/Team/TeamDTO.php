<?php

namespace App\Components\Users\Team;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\Team\Mutators\DTO\Mutator;
use App\Components\CRM\Source\Mutators\DTO\Contracts\SourcedDTOContract;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourcedDTOTrait;
use App\Components\Users\User\UserDTO;
use Illuminate\Support\Collection;

/**
 * Class TeamDTO
 *
 * @package App\Components\Users\Team
 */
class TeamDTO implements PresenterContract, LinksContract, RelationshipsContract, SourcedDTOContract
{
    use PresenterTrait;
    use NestedLinksTrait;
    use SourcedDTOTrait;

    public const ROUTE_NAME_SHOW = 'users.teams.show';

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string|null
     */
    public ?string $description = null;

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
    public UserDTO $owner;

    /**
     * @var Collection
     */
    public Collection $members;

    /**
     * @var Collection
     */
    public Collection $requests;

    /**
     * @var Collection
     */
    public Collection $participants;

    /**
     * @var Collection
     */
    public Collection $schools;

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
    public string $routeParameterName = 'teamId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'name' => $this->name,
                'description' => $this->description,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
                'imported' => $this->imported,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'imported' => $this->imported,
        ];
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        return collect(
            [
                'owner' => $this->owner,
                'members' => $this->members,
                'requests' => $this->requests,
                'sources' => $this->sources,
                'schools' => $this->schools,
                'participants' => $this->participants,
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
