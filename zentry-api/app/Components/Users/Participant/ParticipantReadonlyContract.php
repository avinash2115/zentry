<?php

namespace App\Components\Users\Participant;

use App\Components\Users\Participant\Therapy\TherapyReadonlyContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use Illuminate\Support\Collection;
use DateTime;

/**
 * Interface ParticipantReadonlyContract
 *
 * @package App\Components\Users\Participant
 */
interface ParticipantReadonlyContract extends CRMImportableContract, TimestampableContract
{
    public const GENDER_MALE = 'male';
    public const GENDER_FEMALE = 'female';

    public const GENDERS_AVAILABLE = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    /**
     * @return string|null
     */
    public function email(): ?string;

    /**
     * @return UserReadonlyContract
     */
    public function user(): UserReadonlyContract;

    /**
     * @return string|null
     */
    public function firstName(): ?string;

    /**
     * @return string|null
     */
    public function lastName(): ?string;

    /**
     * @return string
     */
    public function fullName(): string;

    /**
     * @return string
     */
    public function displayName(): string;

    /**
     * @return string|null
     */
    public function phoneCode(): ?string;

    /**
     * @return string|null
     */
    public function phoneNumber(): ?string;

    /**
     * @return string|null
     */
    public function avatar(): ?string;

    /**
     * @return string|null
     */
    public function gender(): ?string;

    /**
     * @return DateTime|null
     */
    public function dob(): ?DateTime;

    /**
     * @return string|null
     */
    public function parentEmail(): ?string;

    /**
     * @return string|null
     */
    public function parentPhoneNumber(): ?string;

    /**
     * @return TherapyReadonlyContract
     */
    public function therapy(): TherapyReadonlyContract;

    /**
     * @return Collection
     */
    public function goals(): Collection;

    /**
     * @return Collection
     */
    public function ieps(): Collection;

    /**
     * @return TeamReadonlyContract|null
     */
    public function team(): ?TeamReadonlyContract;

    /**
     * @return SchoolReadonlyContract|null
     */
    public function school(): ?SchoolReadonlyContract;
}
