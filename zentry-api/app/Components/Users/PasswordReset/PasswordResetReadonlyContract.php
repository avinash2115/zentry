<?php

namespace App\Components\Users\PasswordReset;

use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Contracts\ArchivableContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use DateTime;

/**
 * Interface PasswordResetContract
 *
 * @package App\Components\Users\PasswordReset
 */
interface PasswordResetReadonlyContract extends IdentifiableContract, TimestampableContract
{
    /**
     * @return DateTime
     */
    public function TTL(): DateTime;

    /**
     * @return bool
     */
    public function isExpired(): bool;

    /**
     * @return UserReadonlyContract
     */
    public function user(): UserReadonlyContract;
}
