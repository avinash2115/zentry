<?php

namespace App\Components\Sessions\Session\Poi\Participant;

use App\Components\Sessions\Session\Poi\PoiContract;
use App\Components\Users\Participant\Helpers\ParticipantSubstitution;
use App\Components\Users\Participant\ParticipantReadonlyContract as UsersParticipantReadonlyContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Exception;

/**
 * Class ParticipantEntity
 *
 * @package App\Components\Sessions\Session\Poi\Participant
 */
class ParticipantEntity extends ParticipantSubstitution implements ParticipantContract
{
    use IdentifiableTrait;

    /**
     * @var DateTime
     */
    private DateTime $startedAt;

    /**
     * @var DateTime
     */
    private DateTime $endedAt;

    /**
     * @var PoiContract
     */
    private PoiContract $poi;

    /**
     * @param Identity                         $identity
     * @param PoiContract                      $poi
     * @param UsersParticipantReadonlyContract $participant
     * @param DateTime                         $startedAt
     * @param DateTime                         $endedAt
     *
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        PoiContract $poi,
        UsersParticipantReadonlyContract $participant,
        DateTime $startedAt,
        DateTime $endedAt
    ) {
        parent::__construct($participant);
        $this->setIdentity($identity);

        $this->setPoi($poi)->setStartedAt(toUTC($startedAt))->setEndedAt(toUTC($endedAt));
    }

    /**
     * @inheritDoc
     */
    public function raw(): UsersParticipantReadonlyContract
    {
        return $this->participant();
    }

    /**
     * @inheritDoc
     */
    public function startedAt(): DateTime
    {
        return $this->startedAt;
    }

    /**
     * @inheritDoc
     */
    public function endedAt(): DateTime
    {
        return $this->endedAt;
    }

    /**
     * @param PoiContract $poi
     *
     * @return ParticipantEntity
     */
    private function setPoi(PoiContract $poi): ParticipantEntity
    {
        $this->poi = $poi;

        return $this;
    }

    /**
     * @param DateTime $startedAt
     *
     * @return ParticipantEntity
     */
    private function setStartedAt(DateTime $startedAt): ParticipantEntity
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @param DateTime $endedAt
     *
     * @return ParticipantEntity
     */
    private function setEndedAt(DateTime $endedAt): ParticipantEntity
    {
        $this->endedAt = $endedAt;

        return $this;
    }
}
