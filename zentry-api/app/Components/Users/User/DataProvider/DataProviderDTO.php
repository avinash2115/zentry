<?php

namespace App\Components\Users\User\DataProvider;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\User\DataProvider\Mutators\DTO\Mutator;
use App\Convention\ValueObjects\Config\Config;
use Illuminate\Support\Collection;

/**
 * Class DataProviderDTO
 *
 * @package App\Components\Users\User\DataProvider
 */
class DataProviderDTO implements PresenterContract, LinksContract
{
    use PresenterTrait;
    use NestedLinksTrait;

    public const ROUTE_NAME_SHOW = 'users.data_providers.show';

    /**
     * @var string
     */
    public string $driver;

    /**
     * @var int
     */
    public int $status;

    /**
     * @var Config
     */
    public Config $config;

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
    public string $routeParameterName = 'dataProviderId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'driver' => $this->driver,
                'status' => $this->status,
                'config' => $this->config->toArray(),
                'created_at' => $this->createdAt,
                'updated_at' => $this->updatedAt,
            ]
        );
    }
}
