<?php

namespace App\Components\Users\Tests\Unit\Traits;

use App\Components\Users\Participant\ParticipantContract;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait ParticipantHelperTestTrait
 *
 * @package App\Components\Users\Tests\Unit\Traits
 */
trait ParticipantHelperTestTrait
{
    use HelperTrait;

    /**
     * @return ParticipantContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function participant(): ParticipantContract
    {
        return app()->make(
            ParticipantContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $this->createUser(),
                'team' => null,
                'email' => $this->randEmail(),
                'firstName' => $this->randString(),
                'lastName' => $this->randString(),
                'phoneCode' => null,
                'phoneNumber' => null,
                'avatar' => null,
                'gender' => null,
                'dob' => null,
                'school' => null,
            ]
        );
    }
}
