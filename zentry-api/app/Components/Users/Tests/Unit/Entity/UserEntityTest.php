<?php

namespace App\Components\Users\Tests\Unit\Entity;

use App\Components\Users\User\Profile\ProfileContract;
use App\Components\Users\User\UserContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Tests\Traits\HelperTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Class UserEntityTest
 */
class UserEntityTest extends TestCase
{
    use HelperTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [$this->randEmail(), $this->randString(8), $this->randString(8), $this->randString(8), null, null],
            [$this->randEmail(), $this->randString(), $this->randString(8), $this->randString(8), '1', null],
            [
                $this->randEmail(),
                $this->randString(10),
                $this->randString(8),
                $this->randString(8),
                null,
                '34221423412',
            ],
            [$this->randEmail(), $this->randString(10), $this->randString(8), $this->randString(8), '1', '34221423412'],
        ];
    }

    /**
     * @return array
     * @throws Exception
     */
    public function incorrectDataProvider(): array
    {
        return [
            [
                'test.com',
                $this->randString(8),
                $this->randString(8),
                $this->randString(8),
                null,
                null,
                InvalidArgumentException::class,
            ],
            [
                'test@test.com',
                $this->randString(5),
                $this->randString(8),
                $this->randString(8),
                null,
                null,
                InvalidArgumentException::class,
            ],
            [
                'test@test.com',
                $this->randString(6),
                $this->randString(8),
                $this->randString(8),
                null,
                null,
                InvalidArgumentException::class,
            ],
            [
                'test@test.com',
                $this->randString(7),
                $this->randString(8),
                $this->randString(8),
                null,
                null,
                InvalidArgumentException::class,
            ],
            [
                'test@test.com',
                $this->randString(8),
                '',
                $this->randString(8),
                null,
                null,
                InvalidArgumentException::class,
            ],
            [
                'test@test.com',
                $this->randString(8),
                $this->randString(8),
                '',
                null,
                null,
                InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * @param string      $email
     * @param string      $password
     * @param string      $firstName
     * @param string      $lastName
     * @param string|null $phoneCode
     * @param string|null $phoneNumber
     * @param string      $error
     *
     * @return void
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @dataProvider incorrectDataProvider
     */
    public function testCreateIncorrect(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        ?string $phoneCode,
        ?string $phoneNumber,
        string $error
    ): void {
        $this->expectException($error);
        $user = $this->createUser(
            $this->generateIdentity(),
            new Credentials(new Email($email), new HashedPassword($password))
        );
        $user->attachProfile($this->createProfile($user, new Payload($firstName, $lastName, $phoneCode, $phoneNumber)));
    }

    /**
     * @param string      $email
     * @param string      $password
     * @param string      $firstName
     * @param string      $lastName
     * @param string|null $phoneCode
     * @param string|null $phoneNumber
     *
     * @return UserContract
     * @throws BindingResolutionException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws InvalidArgumentException
     * @dataProvider correctDataProvider
     */
    public function testCreateCorrect(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        ?string $phoneCode,
        ?string $phoneNumber
    ): UserContract {
        $identity = $this->generateIdentity();
        $user = $this->createUser($identity, new Credentials(new Email($email), new HashedPassword($password)));

        static::assertTrue($user->identity()->equals($identity));
        static::assertEquals($user->email(), $email);

        static::assertNotNull($user->updatedAt());
        static::assertNotNull($user->createdAt());

        try {
            $user->profile();
        } catch (InvalidArgumentException $exception) {
            self::assertTrue(true);
        }

        $payload = new Payload($firstName, $lastName, $phoneCode, $phoneNumber);
        $user->attachProfile($this->createProfile($user, $payload));

        static::assertEquals($payload->firstName(), $user->profile()->firstName());
        static::assertEquals($payload->lastName(), $user->profile()->lastName());
        static::assertEquals($payload->phoneCode(), $user->profile()->phoneCode());
        static::assertEquals($payload->phoneNumber(), $user->profile()->phoneNumber());

        try {
            $user->attachProfile($this->createProfile($user, $payload));
        } catch (InvalidArgumentException $exception) {
            self::assertTrue(true);
        }

        return $user;
    }

    /**
     * @param Identity    $id
     * @param Credentials $credentials
     *
     * @return UserContract
     * @throws BindingResolutionException
     */
    private function createUser(
        Identity $id,
        Credentials $credentials
    ): UserContract {
        return $this->app->make(
            UserContract::class,
            [
                'identity' => $id,
                'credentials' => $credentials,
            ]
        );
    }

    /**
     * @param UserContract $user
     * @param Payload      $payload
     *
     * @return ProfileContract
     * @throws BindingResolutionException
     */
    private function createProfile(UserContract $user, Payload $payload): ProfileContract
    {
        return $this->app->make(
            ProfileContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'payload' => $payload,
            ]
        );
    }
}
