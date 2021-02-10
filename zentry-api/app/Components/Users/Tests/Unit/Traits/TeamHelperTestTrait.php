<?php

namespace App\Components\Users\Tests\Unit\Traits;

use App\Components\Users\Team\TeamContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use \Exception;

/**
 * trait TeamHelperTestTrait
 *
 * @package App\Components\Users\Tests\Unit\Traits
 */
trait TeamHelperTestTrait
{
    use HelperTrait;

    /**
     * @param UserReadonlyContract $owner
     * @param Identity             $identity
     *
     * @return TeamContract
     * @throws Exception
     */
    protected function createTeam(
        UserReadonlyContract $owner,
        Identity $identity = null
    ): TeamContract {
        return app()->make(
            TeamContract::class,
            [
                'identity' => ($identity) ? $identity : $this->generateIdentity(),
                'owner' => $owner,
                'name' => $this->randString(),
                'description' => null,
            ]
        );
    }

}