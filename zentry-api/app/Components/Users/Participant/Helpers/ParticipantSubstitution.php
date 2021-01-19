<?php

namespace App\Components\Users\Participant\Helpers;

use App\Components\CRM\Source\ParticipantSourceEntity;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Participant\Therapy\TherapyReadonlyContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Illuminate\Support\Collection;

/**
 * Class ParticipantSubstitution
 *
 * @package App\Components\Users\Participant\Helpers
 */
abstract class ParticipantSubstitution implements ParticipantReadonlyContract
{
    /**
     * @var ParticipantReadonlyContract
     */
    protected ParticipantReadonlyContract $participant;

    /**
     * ParticipantSubstitution constructor.
     *
     * @param ParticipantReadonlyContract $participant
     */
    public function __construct(ParticipantReadonlyContract $participant)
    {
        $this->participant = $participant;
    }

    /**
     * @inheritDoc
     */
    abstract public function identity(): Identity;

    /**
     * @inheritDoc
     */
    public function createdAt(): DateTime
    {
        return $this->participant()->createdAt();
    }

    /**
     * @inheritDoc
     */
    public function updatedAt(): DateTime
    {
        return $this->participant()->updatedAt();
    }

    /**
     * @inheritDoc
     */
    public function email(): ?string
    {
        return $this->participant()->email();
    }

    /**
     * @inheritDoc
     */
    public function user(): UserReadonlyContract
    {
        return $this->participant()->user();
    }

    /**
     * @inheritDoc
     */
    public function firstName(): ?string
    {
        return $this->participant()->firstName();
    }

    /**
     * @inheritDoc
     */
    public function lastName(): ?string
    {
        return $this->participant()->lastName();
    }

    /**
     * @inheritDoc
     */
    public function fullName(): string
    {
        return $this->participant()->fullName();
    }

    /**
     * @inheritDoc
     */
    public function displayName(): string
    {
        return $this->participant()->fullName();
    }

    /**
     * @inheritDoc
     */
    public function phoneCode(): ?string
    {
        return $this->participant()->phoneCode();
    }

    /**
     * @inheritDoc
     */
    public function phoneNumber(): ?string
    {
        return $this->participant()->phoneNumber();
    }

    /**
     * @inheritDoc
     */
    public function avatar(): ?string
    {
        return $this->participant()->avatar();
    }

    /**
     * @inheritDoc
     */
    public function gender(): ?string
    {
        return $this->participant()->gender();
    }

    /**
     * @inheritDoc
     */
    public function dob(): ?DateTime
    {
        return $this->participant()->dob();
    }

    /**
     * @inheritDoc
     */
    public function parentEmail(): ?string
    {
        return $this->participant()->parentEmail();
    }

    /**
     * @inheritDoc
     */
    public function parentPhoneNumber(): ?string
    {
        return $this->participant()->parentPhoneNumber();
    }

    /**
     * @inheritDoc
     */
    public function goals(): Collection
    {
        return $this->participant()->goals();
    }

    /**
     * @inheritDoc
     */
    public function ieps(): Collection
    {
        return $this->participant()->ieps();
    }

    /**
     * @return ParticipantReadonlyContract
     */
    public function participant(): ParticipantReadonlyContract
    {
        return $this->participant;
    }

    /**
     * @inheritDoc
     */
    public function therapy(): TherapyReadonlyContract
    {
        return $this->participant()->therapy();
    }

    /**
     * @inheritDoc
     */
    public function team(): ?TeamReadonlyContract
    {
        return $this->participant()->team();
    }

    /**
     * @inheritDoc
     */
    public function sourceEntityClass(): string
    {
        return $this->participant()->sourceEntityClass();
    }

    /**
     * @inheritDoc
     */
    public static function crmEntityType(): string
    {
        return self::CRM_ENTITY_TYPE_PARTICIPANT;
    }

    /**
     * @inheritDoc
     */
    public function sources(): Collection
    {
        return $this->participant()->sources();
    }

    /**
     * @inheritDoc
     */
    public function school(): ?SchoolReadonlyContract
    {
        return $this->participant()->school();
    }
}
