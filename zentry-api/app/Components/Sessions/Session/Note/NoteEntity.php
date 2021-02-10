<?php

namespace App\Components\Sessions\Session\Note;

use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract as PoiParticipantReadonlyContract;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\ValueObjects\Note\Payload;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class NoteEntity
 *
 * @package App\Components\Sessions\Session\Note
 */
class NoteEntity implements NoteContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

    /**
     * @var string
     */
    public string $text;

    /**
     * @var string|null
     */
    public ?string $url;

    /**
     * @var SessionReadonlyContract
     */
    private SessionReadonlyContract $session;

    /**
     * @var ParticipantReadonlyContract|null
     */
    private ?ParticipantReadonlyContract $participant;

    /**
     * @var PoiReadonlyContract|null
     */
    private ?PoiReadonlyContract $poi;

    /**
     * @var PoiParticipantReadonlyContract|null
     */
    private ?PoiParticipantReadonlyContract $poiParticipant;

    /**
     * @param Identity                $identity
     * @param SessionReadonlyContract $session
     * @param Payload                 $payload
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        SessionReadonlyContract $session,
        Payload $payload
    ) {
        $this->setIdentity($identity);
        $this->session = $session;
        $this->participant = $payload->participant();
        $this->poi = $payload->poi();
        $this->poiParticipant = $payload->poiParticipant();
        $this->setText($payload->text());
        $this->setUrl($payload->url());

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function poi(): ?PoiReadonlyContract
    {
        return $this->poi;
    }

    /**
     * @inheritDoc
     */
    public function changeText(string $text): NoteContract
    {
        return $this->setText($text);
    }

    /**
     * @inheritDoc
     */
    public function text(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return NoteEntity
     */
    private function setText(string $text): NoteEntity
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeUrl(?string $url = null): NoteContract
    {
        return $this->setUrl($url);
    }

    /**
     * @inheritDoc
     */
    public function url(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return NoteEntity
     * @throws InvalidArgumentException
     */
    private function setUrl(?string $url = null): NoteEntity
    {
        if ($url === null && strEmpty($this->text())) {
            throw new InvalidArgumentException("Text or url is required");
        }

        $this->url = $url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function participant(): ?ParticipantReadonlyContract
    {
        return $this->participant;
    }

    /**
     * @inheritDoc
     */
    public function poiParticipant(): ?PoiParticipantReadonlyContract
    {
        return $this->poiParticipant;
    }
}
