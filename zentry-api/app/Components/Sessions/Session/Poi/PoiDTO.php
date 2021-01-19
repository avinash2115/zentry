<?php

namespace App\Components\Sessions\Session\Poi;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Sessions\Services\Transcription\Contracts\InjectedDTO;
use App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator;
use App\Components\Share\Shared\Traits\SharedDTOTrait;
use App\Components\Users\Participant\Traits\AudiencableDTOTrait;
use App\Convention\ValueObjects\Tags;
use Illuminate\Support\Collection;

/**
 * Class PoiDTO
 *
 * @package App\Components\Sessions\Session\Poi
 */
class PoiDTO extends InjectedDTO implements LinksContract
{
    use SharedDTOTrait;
    use PresenterTrait;
    use NestedLinksTrait;
    use AudiencableDTOTrait;

    public const ROUTE_NAME_SHOW = 'sessions.pois.show';

    /**
     * @var string
     */
    public string $type;

    /**
     * @var string|null
     */
    public ?string $name;

    /**
     * @var Tags
     */
    public Tags $tags;

    /**
     * @var string | null
     */
    public ?string $thumbnailURL;

    /**
     * @var int
     */
    public int $duration;

    /**
     * @var string
     */
    public string $startedAt;

    /**
     * @var string
     */
    public string $endedAt;

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
    public string $routeParameterName = 'poiId';

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
                'name' => $this->name,
                'tags' => $this->tags->toArray(),
                'thumbnail_url' => $this->thumbnailURL,
                'duration' => $this->duration,
                'is_shared' => $this->isShared,
                'started_at' => $this->startedAt,
                'ended_at' => $this->endedAt,
                'created_at' => $this->createdAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        $nested = parent::nested();

        $nested->put('participants', $this->participants);

        return $nested;
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return collect();
    }
}
