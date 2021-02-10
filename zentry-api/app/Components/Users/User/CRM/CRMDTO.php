<?php

namespace App\Components\Users\User\CRM;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\User\CRM\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class CRMDTO
 *
 * @package App\Components\Users\User\CRM
 */
class CRMDTO implements PresenterContract, LinksContract
{
    use PresenterTrait;
    use NestedLinksTrait;

    public const ROUTE_NAME_SHOW = 'users.crms.show';

    /**
     * @var string
     */
    public string $driver;

    /**
     * @var bool
     */
    public bool $active;

    /**
     * @var bool
     */
    public bool $notified;

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
    public string $routeParameterName = 'crmId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'driver' => $this->driver,
                'active' => $this->active,
                'notified' => $this->notified,
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
            ]
        );
    }
}
