<?php

namespace App\Components\Users\Tests\Unit\Traits;

use App\Components\Users\User\CRM\CRMContract;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\UserContract;
use App\Components\Users\ValueObjects\CRM\Config\Config;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * trait CRMHelperTestTrait
 *
 * @package App\Components\Users\Tests\Unit\Traits
 */
trait CRMHelperTestTrait
{
    use HelperTrait;

    /**
     * @param UserContract $user
     * @param Identity     $identity
     * @param string       $driver
     * @param Config       $config
     *
     * @return CRMContract
     * @throws BindingResolutionException
     */
    private function createCRM(UserContract $user, Identity $identity = null, string $driver = CRMReadonlyContract::DRIVER_THERAPYLOG, Config $config = null): CRMContract
    {
        return app()->make(CRMContract::class, [
            'identity' => ($identity) ? $identity : $this->generateIdentity(),
            'user' => $user,
            'driver' => $driver,
            'config' => ($config) ? $config : new Config([]),
        ]);
    }

}