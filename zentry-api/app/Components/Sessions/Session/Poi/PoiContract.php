<?php

namespace App\Components\Sessions\Session\Poi;

use App\Components\Sessions\Session\Poi\Participant\ParticipantContract;
use App\Components\Sessions\Session\Poi\Participant\ParticipantReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use App\Convention\ValueObjects\Tags;

/**
 * Interface PoiContract
 *
 * @package App\Components\Sessions\Session\Poi
 */
interface PoiContract extends PoiReadonlyContract
{
    /**
     * @param string|null $name
     *
     * @return PoiContract
     */
    public function changeName(string $name = null): PoiContract;

    /**
     * @param Tags $tags
     *
     * @return PoiContract
     */
    public function changeTags(Tags $tags): PoiContract;

    /**
     * @param string $url
     *
     * @return PoiContract
     */
    public function changeThumbnail(string $url): PoiContract;

    /**
     * @param string $url
     *
     * @return PoiContract
     */
    public function changeStream(string $url): PoiContract;

    /**
     * @param ParticipantReadonlyContract $participant
     *
     * @return PoiContract
     */
    public function addParticipant(ParticipantReadonlyContract $participant): PoiContract;

    /**
     * @param Identity $identity
     *
     * @return ParticipantContract
     * @throws NotFoundException
     */
    public function participantByIdentity(Identity $identity): ParticipantContract;

    /**
     * @param ParticipantReadonlyContract $participant
     *
     * @return PoiContract
     * @throws NotFoundException
     */
    public function removeParticipant(ParticipantReadonlyContract $participant): PoiContract;
}
