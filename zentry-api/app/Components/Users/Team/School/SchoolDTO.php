<?php

namespace App\Components\Users\Team\School;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\Team\School\Mutators\DTO\Mutator;
use App\Components\CRM\Source\Mutators\DTO\Contracts\SourcedDTOContract;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourcedDTOTrait;
use Illuminate\Support\Collection;

/**
 * Class SchoolDTO
 *
 * @package App\Components\Users\Team\School
 */
class SchoolDTO implements PresenterContract, LinksContract, RelationshipsContract, SourcedDTOContract
{
    use PresenterTrait;
    use NestedLinksTrait;
    use SourcedDTOTrait;

    public const ROUTE_NAME_SHOW = 'users.teams.schools.show';

    /**
     * @var string
     */
    public string $name;

    /**
     * @var bool
     */
    public bool $available;

    /**
     * @var string|null
     */
    public ?string $streetAddress = null;

    /**
     * @var string|null
     */
    public ?string $city = null;

    /**
     * @var string|null
     */
    public ?string $state = null;

    /**
     * @var string|null
     */
    public ?string $zip = null;

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var string
     */
    public string $updatedAt;

    /**
     * @var Collection
     */
    public Collection $participants;

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
    public string $routeParameterName = 'schoolId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'name' => $this->name,
                'available' => $this->available,
                'street_address' => $this->streetAddress,
                'city' => $this->city,
                'state' => $this->state,
                'zip' => $this->zip,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
                'imported' => $this->imported,
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
                'participants' => $this->participants,
                'sources' => $this->sources,
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
