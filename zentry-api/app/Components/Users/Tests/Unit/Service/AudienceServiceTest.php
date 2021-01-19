<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Tests\Unit\Traits\SessionHelperTestTrait;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Services\Participant\Audience\AudienceServiceContract;
use App\Components\Users\Services\Participant\Audience\Traits\AudiencableServiceTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Tags;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;

/**
 * Class AudienceServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class AudienceServiceTest extends TestCase
{
    use AudiencableServiceTrait;
    use SessionHelperTestTrait;
    use SessionServiceTrait;

    /**
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws RecursionContextInvalidArgumentException
     * @throws PHPUnitException
     * @throws Exception
     */
    public function testCreate(): void
    {
        $session = $this->sessionService__()->create(
            $user = $this->createUser(),
            [
                'name' => $this->randString(),
                'school' => $this->createSchool($this->createTeam($user)),
            ]
        )->readonly();

        $audiencableService = app()->make(AudienceServiceContract::class, [
            'audiencable' => $session,
            'audiencableService' => $this->sessionService__(),
        ]);

        $participant = $this->makeParticipant();

        $audiencableService->add($participant);

        self::assertCount($session->participants()->count(), $audiencableService->listRO());
        self::assertCount($session->participants()->count(), $audiencableService->list());

        $added = $session->participantByIdentity($participant->identity());

        static::assertEquals($added->user(), $participant->user());
        static::assertEquals($added->user()->identity(), $participant->user()->identity());
        static::assertEquals($added->email(), $participant->email());
        static::assertEquals($added->firstName(), $participant->firstName());
        static::assertEquals($added->lastName(), $participant->lastName());
        static::assertEquals($added->phoneCode(), $participant->phoneCode());
        static::assertEquals($added->phoneNumber(), $participant->phoneNumber());
        static::assertEquals($added->avatar(), $participant->avatar());
    }

    /**
     * @return ParticipantContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function makeParticipant(): ParticipantContract
    {
        return $this->app->make(
            ParticipantContract::class,
            [
                'user' => $this->createUser(),
                'identity' => IdentityGenerator::next(),
                'team' => null,
                'school' => null,
                'email' => null,
                'firstName' => $this->randString(),
                'lastName' => $this->randString(),
                'phoneCode' => '+1',
                'phoneNumber' => $this->randInt(),
                'avatar' => null,
            ]
        );
    }
}
