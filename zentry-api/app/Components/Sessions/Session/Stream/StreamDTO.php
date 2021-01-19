<?php

namespace App\Components\Sessions\Session\Stream;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\LinkParameters;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Sessions\Session\Stream\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class StreamDTO
 *
 * @package App\Components\Sessions\Session\Stream
 */
class StreamDTO implements PresenterContract, LinksContract
{
    use PresenterTrait;
    use NestedLinksTrait;

    public const ROUTE_NAME_SHOW = 'sessions.streams.show';

    /**
     * @var string
     */
    public string $type;

    /**
     * @var int
     */
    public int $convertProgress;

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var string
     */
    public string $route = self::ROUTE_NAME_SHOW;

    /**
     * @var string
     */
    public string $routeParameterName = 'streamId';

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
                'type' => $this->type,
                'convert_progress' => $this->convertProgress,
                'created_at' => $this->createdAt,
            ]
        );
    }
}
