<?php

namespace App\Components\Users\Team\Request;

use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Contracts\HasCreatedAt;
use App\Convention\Entities\Contracts\IdentifiableContract;

/**
 * Interface RequestReadonlyContract
 *
 * @package App\Components\Users\Team\Request
 */
interface RequestReadonlyContract extends IdentifiableContract, HasCreatedAt
{
    /**
     * @return UserReadonlyContract
     */
    public function user(): UserReadonlyContract;
}
