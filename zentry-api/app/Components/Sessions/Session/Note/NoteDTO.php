<?php

namespace App\Components\Sessions\Session\Note;

use App\Components\Sessions\Session\Note\Mutators\DTO\Mutator;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Sessions\Session\Poi\Participant\ParticipantDTO as PoiParticipantDTO;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Users\Participant\ParticipantDTO;
use Illuminate\Support\Collection;

/**
 * Class NoteDTO
 *
 * @package App\Components\Sessions\Session\Note
 */
class NoteDTO implements PresenterContract, RelationshipsContract
{
    use PresenterTrait;

    public const ROUTE_NAME_SHOW = 'sessions.notes.show';

    /**
     * @var string
     */
    public string $text;

    /**
     * @var string|null
     */
    public ?string $url = null;

    /**
     * @var string
     */
    public string $createdAt;

    /**
     * @var string
     */
    public string $updatedAt;

    /**
     * @var ParticipantDTO|null
     */
    public ?ParticipantDTO $participant = null;

    /**
     * @var PoiDTO|null
     */
    public ?PoiDTO $poi = null;

    /**
     * @var PoiParticipantDTO|null
     */
    public ?PoiParticipantDTO $poiParticipant = null;

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
    public string $routeParameterName = 'noteId';

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'text' => $this->text,
                'url' => $this->url,
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
        $relationships = collect();

        if ($this->participant instanceof ParticipantDTO) {
            $relationships->put('participant', $this->participant);
        }

        if ($this->poi instanceof PoiDTO) {
            $relationships->put('poi', $this->poi);
        }

        if ($this->poiParticipant instanceof PoiParticipantDTO) {
            $relationships->put('poi_participant', $this->poiParticipant);
        }

        return $relationships;
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return $this->nested();
    }
}
