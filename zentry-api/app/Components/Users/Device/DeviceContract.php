<?php

namespace App\Components\Users\Device;

use App\Components\Users\User\UserReadonlyContract;

/**
 * Interface DeviceContract
 *
 * @package App\Components\Users\Device
 */
interface DeviceContract extends DeviceReadonlyContract
{
    /**
     * @param UserReadonlyContract $user
     *
     * @return DeviceContract
     */
    public function transfer(UserReadonlyContract $user): DeviceContract;
}
