<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\Team\School\SchoolContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\Tests\Unit\Traits\ParticipantHelperTestTrait;
use App\Components\Users\Tests\Unit\Traits\TeamHelperTestTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Class SchoolEntityTest
 *
 * @package App\Components\Users\Tests\Unit\Entity
 */
class SchoolEntityTest extends TestCase
{
    use TeamHelperTestTrait;
    use ParticipantHelperTestTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [$this->randString(), true, null, null, null, null],
            [$this->randString(), false, null, null, null, null],
            [$this->randString(), true, $this->randString(), null, null, null],
            [$this->randString(), false, $this->randString(), $this->randString(), null, null],
            [$this->randString(), true, $this->randString(), $this->randString(), $this->randString(), null],
            [
                $this->randString(),
                false,
                $this->randString(),
                $this->randString(),
                $this->randString(),
                $this->randString(),
            ],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            ['', InvalidArgumentException::class],
        ];
    }

    /**
     * @param string $name
     * @param string $error
     *
     * @return void
     * @throws BindingResolutionException
     * @throws Exception
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(string $name, string $error): void
    {
        $this->expectException($error);
        $owner = $this->createUser();
        $team = $this->createTeam($owner);

        $this->make($this->generateIdentity(), $team, $name);
    }

    /**
     * @param string      $name
     * @param bool        $available
     * @param string|null $streetAddress
     * @param string|null $city
     * @param string|null $state
     * @param string|null $zip
     *
     * @throws BindingResolutionException
     * @throws Exception
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $name,
        bool $available,
        string $streetAddress = null,
        string $city = null,
        string $state = null,
        string $zip = null
    ): void {
        $identity = $this->generateIdentity();

        $owner = $this->createUser();
        $team = $this->createTeam($owner);

        $entity = $this->make($identity, $team, $name, $available, $streetAddress, $city, $state, $zip);

        static::assertTrue($entity->identity()->equals($identity));
        static::assertEquals($entity->name(), $name);
        static::assertEquals($entity->available(), $available);
        static::assertEquals($entity->streetAddress(), $streetAddress);
        static::assertEquals($entity->city(), $city);
        static::assertEquals($entity->state(), $state);
        static::assertEquals($entity->zip(), $zip);

        static::assertNotNull($entity->createdAt());

        self::assertCount(0, $entity->participants());

        $newStreetAddress = $this->randString();
        $entity->changeStreetAddress($newStreetAddress);
        self::assertEquals($newStreetAddress, $entity->streetAddress());
// 
        $newCity = $this->randString();
        $entity->changeCity($newCity);
        self::assertEquals($newCity, $entity->city());

        $newState = $this->randString();
        $entity->changeState($newState);
        self::assertEquals($newState, $entity->state());

        $newZip = $this->randString();
        $entity->changeZip($newZip);
        self::assertEquals($newZip, $entity->zip());

        $participant = $this->participant();
        $entity->addParticipant($participant);
        self::assertCount(1, $entity->participants());
        self::assertEquals(
            $participant->identity(),
            $entity->participantByIdentity($participant->identity())->identity()
        );

        $entity->removeParticipant($participant);
        self::assertCount(0, $entity->participants());
    }

    /**
     * @param Identity             $id
     * @param TeamReadonlyContract $team
     * @param string               $name
     * @param bool                 $available
     * @param string|null          $streetAddress
     * @param string|null          $city
     * @param string|null          $state
     * @param string|null          $zip
     *
     * @return SchoolContract
     * @throws BindingResolutionException
     */
    protected function make(
        Identity $id,
        TeamReadonlyContract $team,
        string $name,
        bool $available = true,
        string $streetAddress = null,
        string $city = null,
        string $state = null,
        string $zip = null
    ): SchoolContract {
        return app()->make(
            SchoolContract::class,
            [
                'team' => $team,
                'identity' => $id,
                'name' => $name,
                'available' => $available,
                'streetAddress' => $streetAddress,
                'city' => $city,
                'state' => $state,
                'zip' => $zip,
            ]
        );
    }
}
