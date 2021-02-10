<?php

namespace App\Components\Sessions\Session\Note;

use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract as PoiParticipantReadonlyContract;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface NoteReadonlyContract
 *
 * @package App\Components\Sessions\Session\Note
 */
interface NoteReadonlyContract extends IdentifiableContract, TimestampableContract
{
    /**
     * @return string
     */
    public function text(): string;

    /**
     * @return string|null
     */
    public function url(): ?string;

    /**
     * @return ParticipantReadonlyContract|null
     */
    public function participant(): ?ParticipantReadonlyContract;

    /**
     * @return PoiReadonlyContract|null
     */
    public function poi(): ?PoiReadonlyContract;

    /**
     * @return PoiParticipantReadonlyContract|null
     */
    public function poiParticipant(): ?PoiParticipantReadonlyContract;
}
