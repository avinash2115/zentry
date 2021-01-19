<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Tests\Traits\HelperTrait;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use PHPUnit\Framework\Exception as PHPUnitException;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use Tests\TestCase;
use UnexpectedValueException;

/**
 * Class ParticipantServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class ParticipantServiceTest extends TestCase
{
    use HelperTrait;
    use ParticipantServiceTrait;
    use AuthServiceTrait;

    /**
     * @return array
     * @throws Exception
     */
    public function correctDataProvider(): array
    {
        return [
            [
                $this->randString() . '@example.com',
                $this->randString(),
                $this->randString(),
                '+1',
                $this->randString(),
                $this->randString(),
            ],
            [
                $this->randString() . '@example.com',
                $this->randString(),
                $this->randString(),
                '+1',
                $this->randString(),
                null,
            ],
            [
                $this->randString() . '@example.com',
                $this->randString(),
                $this->randString(),
                '+1',
                null,
                $this->randString(),
            ],
            [
                $this->randString() . '@example.com',
                $this->randString(),
                $this->randString(),
                null,
                $this->randString(),
                $this->randString(),
            ],
            [
                $this->randString() . '@example.com',
                $this->randString(),
                null,
                '+1',
                $this->randString(),
                $this->randString(),
            ],
            [
                $this->randString() . '@example.com',
                null,
                $this->randString(),
                '+1',
                $this->randString(),
                $this->randString(),
            ],
            [null, $this->randString(), $this->randString(), '+1', $this->randString(), $this->randString()],
        ];
    }

    /**
     * @dataProvider correctDataProvider
     *
     * @param string      $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $phoneCode
     * @param string|null $phoneNumber
     * @param string|null $avatar
     *
     * @throws BindingResolutionException
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RecursionContextInvalidArgumentException
     * @throws UnexpectedValueException
     * @throws PHPUnitException
     * @throws NoResultException
     * @throws Exception
     */
    public function testCreate(
        string $email = null,
        string $firstName = null,
        string $lastName = null,
        string $phoneCode = null,
        string $phoneNumber = null,
        string $avatar = null
    ): void {
        $this->login();
        $user = $this->authService__()->user()->readonly();

        $entity = $this->participantService__()->create(
            $user,
            [
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_code' => $phoneCode,
                'phone_number' => $phoneNumber,
                'avatar' => $avatar,
            ]
        )->readonly();

        static::assertEquals($user, $entity->user());
        static::assertEquals($user->identity(), $entity->user()->identity());
        static::assertEquals($email, $entity->email());
        static::assertEquals($firstName, $entity->firstName());
        static::assertEquals($lastName, $entity->lastName());
        static::assertEquals($phoneCode, $entity->phoneCode());
        static::assertEquals($phoneNumber, $entity->phoneNumber());
        static::assertEquals($avatar, $entity->avatar());

        $email = $this->randString().'@test.com';
        $firstName = $this->randString();
        $lastName = $this->randString();
        $phoneCode = '+38';
        $phoneNumber = $this->randInt();
        $avatar = $this->randString();

        $dto = $this->participantService__()->workWith($entity->identity())->change(
            [
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone_code' => $phoneCode,
                'phone_number' => $phoneNumber,
                'avatar' => $avatar,
            ]
        )->dto();

        static::assertEquals($email, $dto->email);
        static::assertEquals($firstName, $dto->firstName);
        static::assertEquals($lastName, $dto->lastName);
        static::assertEquals($phoneCode, $dto->phoneCode);
        static::assertEquals($phoneNumber, $dto->phoneNumber);
        static::assertEquals($avatar, $dto->avatar);

        self::assertCount(1, $this->participantService__()->listRO());
        self::assertCount(1, $this->participantService__()->list());

        $this->participantService__()->remove();

        self::assertCount(0, $this->participantService__()->listRO());
        self::assertCount(0, $this->participantService__()->list());
    }

    /**
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     */
    public function testWorkWithException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->participantService__()->workWith((string)$this->generateIdentity());
    }
}
