<?php

namespace App\Components\Users\Participant\IEP;

use App\Components\CRM\Source\ParticipantIEPSourceEntity;
use App\Components\CRM\Source\Traits\HasSourceTrait;
use App\Components\Users\Participant\ParticipantContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Exception;
use InvalidArgumentException;

/**
 * Class IEPEntity
 *
 * @package App\Components\Users\Participant\IEP
 */
class IEPEntity implements IEPContract
{
    use IdentifiableTrait;
    use TimestampableTrait;
    use HasSourceTrait;
    use CollectibleTrait;

    /**
     * @var DateTime
     */
    private DateTime $dateActual;

    /**
     * @var DateTime
     */
    private DateTime $dateReeval;

    /**
     * @var ParticipantContract
     */
    private ParticipantContract $participant;

    /**
     * IEPEntity constructor.
     *
     * @param Identity            $identity
     * @param ParticipantContract $participant
     * @param DateTime            $dateActual
     * @param DateTime            $dateReeval
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function __construct(
        Identity $identity,
        ParticipantContract $participant,
        DateTime $dateActual,
        DateTime $dateReeval
    ) {
        $this->setIdentity($identity);

        $this->changeDateActual($dateActual);
        $this->changeDateReeval($dateReeval);

        $this->setSources();

        $this->participant = $participant;

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(): string
    {
        return ParticipantIEPSourceEntity::class;
    }

    /**
     * @inheritDoc
     */
    public static function crmEntityType(): string
    {
        return self::CRM_ENTITY_TYPE_PARTICIPANT_IEP;
    }

    /**
     * @inheritDoc
     */
    public function changeDateActual(DateTime $value): IEPContract
    {
        $this->dateActual = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function changeDateReeval(DateTime $value): IEPContract
    {
        $this->dateReeval = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dateActual(): DateTime
    {
        return $this->dateActual;
    }

    /**
     * @inheritDoc
     */
    public function dateReeval(): DateTime
    {
        return $this->dateReeval;
    }
}
