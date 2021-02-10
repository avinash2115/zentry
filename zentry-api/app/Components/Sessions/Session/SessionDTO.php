<?php

namespace App\Components\Sessions\Session;

use App\Components\CRM\Source\Mutators\DTO\Contracts\SourcedDTOContract;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourcedDTOTrait;
use App\Components\Services\Service\ServiceDTO;
use App\Components\Sessions\Session\Mutators\DTO\Mutator;
use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\JsonApi\Traits\NestedLinksTrait;
use App\Components\Sessions\ValueObjects\Geo;
use App\Components\Share\Shared\SharedDTO;
use App\Components\Share\Shared\Traits\SharedDTOTrait;
use App\Components\Users\Participant\Traits\AudiencableDTOTrait;
use App\Components\Users\Team\School\SchoolDTO;
use App\Components\Users\User\UserDTO;
use App\Convention\ValueObjects\Tags;
use Illuminate\Support\Collection;

/**
 * Class SessionDTO
 *
 * @package App\Components\Sessions\Session
 */
class SessionDTO extends SharedDTO implements LinksContract, RelationshipsContract, SourcedDTOContract
{
    use NestedLinksTrait;
    use AudiencableDTOTrait;
    use SharedDTOTrait;
    use SourcedDTOTrait;

    public const ROUTE_NAME_SHOW = 'sessions.show';

    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $type;

    /**
     * @var int
     */
    public int $status;

    /**
     * @var string
     */
    public string $description;

    /**
     * @var Geo|null
     */
    public ?Geo $geo;

    /**
     * @var Tags
     */
    public Tags $tags;

    /**
     * @var string | null
     */
    public ?string $thumbnailURL = null;

    /**
     * @var string|null
     */
    public ?string $startedAt;

    /**
     * @var string|null
     */
    public ?string $endedAt;

    /**
     * @var string|null
     */
    public ?string $scheduledOn;

    /**
     * @var string|null
     */
    public ?string $scheduledTo;

    /**
     * @var string | null
     */
    public ?string $sign = null;

    /**
     * @var array
     */
    public array $excludedGoals = [];

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
     * @var SchoolDTO|null
     */
    public ?SchoolDTO $school;

    /**
     * @var ServiceDTO|null
     */
    public ?ServiceDTO $service;

    /**
     * @var Collection
     */
    public Collection $pois;

    /**
     * @var Collection
     */
    public Collection $streams;

    /**
     * @var Collection
     */
    public Collection $progress;

    /**
     * @var Collection
     */
    public Collection $goals;

    /**
     * @var Collection
     */
    public Collection $notes;

    /**
     * @var Collection
     */
    public Collection $soaps;

    /**
     * @var string
     */
    public string $route = self::ROUTE_NAME_SHOW;

    /**
     * @var string
     */
    public string $routeParameterName = 'sessionId';

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
                'type' => $this->type,
                'status' => $this->status,
                'description' => $this->description,
                'geo' => $this->geo instanceof Geo ? $this->geo->toArray() : null,
                'tags' => $this->tags->toArray(),
                'thumbnail_url' => $this->thumbnailURL,
                'is_shared' => $this->isShared,
                'started_at' => $this->startedAt,
                'ended_at' => $this->endedAt,
                'scheduled_on' => $this->scheduledOn,
                'scheduled_to' => $this->scheduledTo,
                'sign' => $this->sign,
                'excluded_goals' => $this->excludedGoals,
                'imported' => $this->imported,
                'exported' => $this->exported,
                'updated_at' => $this->updatedAt,
                'created_at' => $this->createdAt,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        $nested = collect(
            [
                'user' => $this->user,
                'notes' => $this->notes,
                'soaps' => $this->soaps,
                'pois' => $this->pois,
                'streams' => $this->streams,
                'participants' => $this->participants,
                'progress' => $this->progress,
                'goals' => $this->goals,
                'sources' => $this->sources,
            ]
        );

        if ($this->school instanceof SchoolDTO) {
            $nested->put('school', $this->school);
        }

        if ($this->service instanceof ServiceDTO) {
            $nested->put('service', $this->service);
        }

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
