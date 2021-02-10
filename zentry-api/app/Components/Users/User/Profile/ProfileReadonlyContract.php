<?php

namespace App\Components\Users\User\Profile;

use App\Convention\Entities\Contracts\{TimestampableContract, IdentifiableContract};

/**
 * Interface ProfileReadonlyContract
 *
 * @package App\Components\Users\User\Profile
 */
interface ProfileReadonlyContract extends IdentifiableContract, TimestampableContract
{
    /**
     * @return string
     */
    public function firstName(): string;

    /**
     * @return string
     */
    public function lastName(): string;

    /**
     * @return string|null
     */
    public function phoneCode(): ?string;

    /**
     * @return string|null
     */
    public function phoneNumber(): ?string;

    /**
     * @return string
     */
    public function displayName(): string;
}
