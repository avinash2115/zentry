<?php

namespace App\Components\Sessions\Session;

use App\Components\Services\Service\ServiceReadonlyContract;
use App\Components\Sessions\Session\Goal\GoalContract;
use App\Components\Sessions\Session\Note\NoteContract;
use App\Components\Sessions\Session\Poi\PoiContract;
use App\Components\Sessions\Session\Progress\ProgressContract;
use App\Components\Sessions\Session\SOAP\SOAPContract;
use App\Components\Sessions\Session\Stream\StreamContract;
use App\Components\Sessions\ValueObjects\Geo;
use App\Components\Users\Participant\Contracts\AudiencableContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use App\Convention\ValueObjects\Tags;
use DateTime;
use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface SessionContract
 *
 * @package App\Components\Sessions\Session
 */
interface SessionContract extends SessionReadonlyContract, AudiencableContract
{
    /**
     * @param PoiContract $poi
     *
     * @return SessionContract
     * @throws RuntimeException
     */
    public function addPoi(PoiContract $poi): SessionContract;

    /**
     * @param StreamContract $stream
     *
     * @return SessionContract
     * @throws RuntimeException
     */
    public function addStream(StreamContract $stream): SessionContract;

    /**
     * @param ProgressContract $progress
     *
     * @return SessionContract
     * @throws RuntimeException
     */
    public function addProgress(ProgressContract $progress): SessionContract;

    /**
     * @param GoalContract $progress
     *
     * @return SessionContract
     */
    public function addGoal(GoalContract $progress): SessionContract;

    /**
     * @param NoteContract $entity
     *
     * @return SessionContract
     * @throws RuntimeException
     */
    public function addNote(NoteContract $entity): SessionContract;

    /**
     * @param SOAPContract $entity
     *
     * @return SessionContract
     * @throws RuntimeException
     */
    public function addSOAP(SOAPContract $entity): SessionContract;

    /**
     * @param string $name
     *
     * @return SessionContract
     * @throws InvalidArgumentException
     */
    public function changeName(string $name): SessionContract;

    /**
     * @param string $type
     *
     * @return SessionContract
     * @throws InvalidArgumentException
     */
    public function changeType(string $type): SessionContract;

    /**
     * @param string $description
     *
     * @return SessionContract
     */
    public function changeDescription(string $description): SessionContract;

    /**
     * @param Geo|null $geo
     *
     * @return SessionContract
     * @throws InvalidArgumentException
     */
    public function changeGeo(?Geo $geo): SessionContract;

    /**
     * @param Tags $tags
     *
     * @return SessionContract
     */
    public function changeTags(Tags $tags): SessionContract;

    /**
     * @param string $url
     *
     * @return SessionContract
     */
    public function changeThumbnail(string $url): SessionContract;

    /**
     * @param DateTime|null $scheduledOn
     *
     * @return SessionEntity
     */
    public function changeScheduledOn(?DateTime $scheduledOn): SessionEntity;

    /**
     * @param DateTime|null $scheduledTo
     *
     * @return SessionEntity
     * @throws InvalidArgumentException
     */
    public function changeScheduledTo(?DateTime $scheduledTo): SessionEntity;

    /**
     * @param string | null $value
     *
     * @return SessionContract
     */
    public function changeSign(?string $value): SessionContract;

    /**
     * @param array $value
     *
     * @return SessionContract
     */
    public function changeExcludedGoals(array $value): SessionContract;

    /**
     * @param ServiceReadonlyContract|null $entity
     *
     * @return SessionContract
     */
    public function changeService(?ServiceReadonlyContract $entity): SessionContract;

    /**
     * @param SchoolReadonlyContract|null $entity
     *
     * @return SessionContract
     */
    public function changeSchool(?SchoolReadonlyContract $entity): SessionContract;

    /**
     * @return SessionContract
     * @throws Exception
     */
    public function touch(): SessionContract;

    /**
     * @return SessionContract
     * @throws InvalidArgumentException
     */
    public function start(): SessionContract;

    /**
     * @return SessionContract
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function end(): SessionContract;

    /**
     * @return SessionContract
     * @throws InvalidArgumentException
     */
    public function wrap(): SessionContract;

    /**
     * @param Identity $identity
     *
     * @return PoiContract
     * @throws NotFoundException
     */
    public function poiByIdentity(Identity $identity): PoiContract;

    /**
     * @param PoiContract $poi
     *
     * @return SessionContract
     * @throws NotFoundException
     */
    public function removePoi(PoiContract $poi): SessionContract;

    /**
     * @param Identity $identity
     *
     * @return StreamContract
     * @throws NotFoundException
     */
    public function streamByIdentity(Identity $identity): StreamContract;

    /**
     * @param string $type
     *
     * @return StreamContract
     * @throws NotFoundException
     */
    public function streamByType(string $type): StreamContract;

    /**
     * @param StreamContract $stream
     *
     * @return SessionContract
     * @throws NotFoundException
     */
    public function removeStream(StreamContract $stream): SessionContract;

    /**
     * @param Identity $identity
     *
     * @return ProgressContract
     * @throws NotFoundException
     */
    public function progressByIdentity(Identity $identity): ProgressContract;

    /**
     * @param ProgressContract $progress
     *
     * @return SessionContract
     * @throws NotFoundException
     */
    public function removeProgress(ProgressContract $progress): SessionContract;

    /**
     * @param Identity $identity
     *
     * @return GoalContract
     * @throws NotFoundException
     */
    public function goalByIdentity(Identity $identity): GoalContract;

    /**
     * @param GoalContract $progress
     *
     * @return SessionContract
     * @throws NotFoundException
     */
    public function removeGoal(GoalContract $progress): SessionContract;

    /**
     * @param Identity $identity
     *
     * @return NoteContract
     * @throws NotFoundException
     */
    public function noteByIdentity(Identity $identity): NoteContract;

    /**
     * @param NoteContract $entity
     *
     * @return SessionContract
     * @throws NotFoundException
     */
    public function removeNote(NoteContract $entity): SessionContract;

    /**
     * @param Identity $identity
     *
     * @return SOAPContract
     * @throws NotFoundException
     */
    public function SOAPByIdentity(Identity $identity): SOAPContract;

    /**
     * @param SOAPContract $entity
     *
     * @return SessionContract
     * @throws NotFoundException
     */
    public function removeSOAP(SOAPContract $entity): SessionContract;
}
