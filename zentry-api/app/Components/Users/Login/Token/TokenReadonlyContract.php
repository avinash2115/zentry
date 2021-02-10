<?php

namespace App\Components\Users\Login\Token;

use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Contracts\HasCreatedAt;
use App\Convention\Entities\Contracts\IdentifiableContract;

/**
 * Interface TokenReadonlyContract
 *
 * @package App\Components\Users\Login\Token
 */
interface TokenReadonlyContract extends IdentifiableContract, HasCreatedAt
{
    /**
     * @return string
     */
    public function referer(): string;

    /**
     * @return UserReadonlyContract
     */
    public function user(): UserReadonlyContract;
}
