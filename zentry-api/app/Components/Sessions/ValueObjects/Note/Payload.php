<?php

namespace App\Components\Sessions\ValueObjects\Note;

use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract as PoiParticipantReadonlyContract;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;

/**
 * Class Payload
 *
 * @package App\Components\Sessions\ValueObjects\Note
 */
class Payload
{
    /**
     * @var string
     */
    private string $text;

    /**
     * @var string|null
     */
    private ?string $url;

    /**
     * @var ParticipantReadonlyContract
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
     * @param ParticipantReadonlyContract    $participant
     * @param PoiReadonlyContract            $poi
     * @param PoiParticipantReadonlyContract $poiParticipant
     * @param string|null                    $text
     * @param string|null                    $url
     */
    public function __construct(
        string $text = null,
        string $url = null,
        ?ParticipantReadonlyContract $participant = null,
        ?PoiReadonlyContract $poi = null,
        ?PoiParticipantReadonlyContract $poiParticipant = null
    ) {
        $this->participant = $participant;
        $this->poi = $poi;
        $this->poiParticipant = $poiParticipant;
        $this->setText($text);
        $this->setUrl($url);
    }

    /**
     * @return string
     */
    public function text(): string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     *
     * @return Payload
     */
    private function setText(?string $text): Payload
    {
        if ($text === null) {
            $text = '';
        }

        $this->text = $text;

        return $this;
    }

    /**
     * @return string|null
     */
    public function url(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     *
     * @return Payload
     */
    private function setUrl(?string $url): Payload
    {
        $this->url = $url;


        return $this;
    }

    /**
     * @param string|null $url
     *
     * @return Payload
     */
    public function changeUrl(?string $url): Payload
    {
        return $this->setUrl($url);
    }

    /**
     * @return ParticipantReadonlyContract|null
     */
    public function participant(): ?ParticipantReadonlyContract
    {
        return $this->participant;
    }

    /**
     * @return PoiReadonlyContract|null
     */
    public function poi(): ?PoiReadonlyContract
    {
        return $this->poi;
    }

    /**
     * @return PoiParticipantReadonlyContract|null
     */
    public function poiParticipant(): ?PoiParticipantReadonlyContract
    {
        return $this->poiParticipant;
    }
}
