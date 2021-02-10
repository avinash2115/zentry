<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\Tests\Unit\Traits\TeamHelperTestTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;

/**
 * Class ParticipantEntityTest
 *
 * @package App\Components\Users\Tests\Unit\Entity
 */
class ParticipantEntityTest extends TestCase
{
    use TeamHelperTestTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [$this->randString() . '@example.com', $this->randString(), $this->randString(), '+1', $this->randString(), $this->randString()],
            [$this->randString() . '@example.com', $this->randString(), $this->randString(), '+1', $this->randString(), null],
            [$this->randString() . '@example.com', $this->randString(), $this->randString(), '+1', null, $this->randString()],
            [$this->randString() . '@example.com', $this->randString(), $this->randString(), null, $this->randString(), $this->randString()],
            [$this->randString() . '@example.com', $this->randString(), null, '+1', $this->randString(), $this->randString()],
            [$this->randString() . '@example.com', null, $this->randString(), '+1', $this->randString(), $this->randString()],
            [null, $this->randString(), $this->randString(), '+1', $this->randString(), $this->randString()],
            [$this->randString() . '@example.com', null, $this->randString(), '+1', $this->randString(), $this->randString(), true],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            [null, null, null, null, null, null],
            [null, null, null, '+1', $this->randString(), $this->randString()],
        ];
    }

    /**
     * @param string|null $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $phoneCode
     * @param string|null $phoneNumber
     * @param string|null $avatar
     * @param bool $withTeam
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     * @throws InvalidArgumentException
     * @throws Exception
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $email = null,
        string $firstName = null,
        string $lastName = null,
        string $phoneCode = null,
        string $phoneNumber = null,
        string $avatar = null,
        bool $withTeam = false
    ): void
    {
        $identity = $this->generateIdentity();
        $user = $this->createUser();
        $team = ($withTeam) ? $this->createTeam($user) : null;
        $entity = $this->create($identity, $user, $team, null, $email, $firstName, $lastName, $phoneCode, $phoneNumber, $avatar);

        static::assertTrue($entity->identity()->equals($identity));

        static::assertEquals($user, $entity->user());
        static::assertEquals($user->identity(), $entity->user()->identity());
        static::assertEquals($email, $entity->email());
        static::assertEquals($firstName, $entity->firstName());
        static::assertEquals($lastName, $entity->lastName());
        static::assertEquals($phoneCode, $entity->phoneCode());
        static::assertEquals($phoneNumber, $entity->phoneNumber());
        static::assertEquals($avatar, $entity->avatar());

        if ($withTeam) {
            static::assertTrue($entity->team() instanceof TeamReadonlyContract);
            static::assertEquals($team, $entity->team());
        }

        $newAvatar = $this->randString();
        $entity->changeAvatar($newAvatar);
        self::assertEquals($newAvatar, $entity->avatar());

        $newEmail = $this->randString().'@example.com';
        $entity->changeEmail($newEmail);
        self::assertEquals($newEmail, $entity->email());

        $newFname = $this->randString();
        $entity->changeFirstName($newFname);
        self::assertEquals($newFname, $entity->firstName());

        $newLname = $this->randString();
        $entity->changeLastName($newLname);
        self::assertEquals($newLname, $entity->lastName());

        static::assertNotNull($entity->createdAt());
        static::assertNotNull($entity->updatedAt());

        $this->expectException(InvalidArgumentException::class);
        $entity->changeFirstName(null);
        $entity->changeLastName(null);
        $entity->changeEmail(null);
    }

    /**
     * @param string|null $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $phoneCode
     * @param string|null $phoneNumber
     * @param string|null $avatar
     *
     * @return void
     * @throws BindingResolutionException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $email = null,
        string $firstName = null,
        string $lastName = null,
        string $phoneCode = null,
        string $phoneNumber = null,
        string $avatar = null
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $this->create($this->generateIdentity(), $this->createUser(), null, null, $email, $firstName, $lastName, $phoneCode, $phoneNumber, $avatar);
    }

    /**
     * @param Identity                    $identity
     * @param UserReadonlyContract        $user
     * @param TeamReadonlyContract|null   $team
     * @param SchoolReadonlyContract|null $school
     * @param string|null                 $email
     * @param string|null                 $firstName
     * @param string|null                 $lastName
     * @param string|null                 $phoneCode
     * @param string|null                 $phoneNumber
     * @param string|null                 $avatar
     *
     * @return ParticipantContract
     * @throws BindingResolutionException
     */
    private function create(
        Identity $identity,
        UserReadonlyContract $user,
        ?TeamReadonlyContract $team = null,
        ?SchoolReadonlyContract $school = null,
        string $email = null,
        string $firstName = null,
        string $lastName = null,
        string $phoneCode = null,
        string $phoneNumber = null,
        string $avatar = null
    ): ParticipantContract
    {
        return $this->app->make(
            ParticipantContract::class,
            [
                'user' => $user,
                'identity' => $identity,
                'team' => $team,
                'school' => $school,
                'email' => $email,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'phoneCode' => $phoneCode,
                'phoneNumber' => $phoneNumber,
                'avatar' => $avatar,
                'gender' => null,
                'dob' => null,
                'parentEmail' => null,
                'parentPhoneNumber' => null,
            ]
        );
    }
}
