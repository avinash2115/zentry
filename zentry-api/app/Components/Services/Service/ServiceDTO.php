<?php

namespace App\Components\Services\Service;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\CRM\Source\Mutators\DTO\Contracts\SourcedDTOContract;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourcedDTOTrait;
use App\Components\Services\Service\Mutators\DTO\Mutator;
use App\Components\Users\User\UserDTO;
use Illuminate\Support\Collection;

/**
 * Class ServiceDTO
 *
 * @package App\Components\Services\Service
 */
class ServiceDTO implements PresenterContract, RelationshipsContract, LinksContract, SourcedDTOContract
{
    use PresenterTrait;
    use NestedLinksTrait;
    use SourcedDTOTrait;

    public const ROUTE_NAME_SHOW = 'services.show';

    /**
     * @var string
     */
    public string $name;

     /**
     * @var string
     */
    public ?string $code = null;

     /**
     * @var string
     */
    public ?string $category = null;

     /**
     * @var string
     */
    public ?string $status = null;

     /**
     * @var string
     */
    public ?string $actions = null;

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
    public string $route = self::ROUTE_NAME_SHOW;

    /**
     * @var string
     */
    public string $routeParameterName = 'serviceId';

    /**
     * @var string
     */
    public string $_type = Mutator::TYPE;

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'name' => $this->name,
                'code' => $this->code,
                'category' => $this->category,
                'status' => $this->status,
                'actions' => $this->actions,


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
