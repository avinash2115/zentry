<?php

namespace App\Components\Users\Device;

use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface DeviceReadonlyContract
 *
 * @package App\Components\Users\Device
 */
interface DeviceReadonlyContract extends IdentifiableContract, TimestampableContract
{
    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return string
     */
    public function model(): string;

    /**
     * @return string
     */
    public function reference(): string;

    /**
     * @return UserReadonlyContract
     */
    public function user(): UserReadonlyContract;
}
