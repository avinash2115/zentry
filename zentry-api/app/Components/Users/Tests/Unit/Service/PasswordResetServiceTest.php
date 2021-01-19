<?php

namespace App\Components\Users\Tests\Unit\Service;

use App\Components\Users\Exceptions\ResetPassword\TokenExpiredException;
use App\Components\Users\PasswordReset\Mutators\DTO\Mutator;
use App\Components\Users\PasswordReset\PasswordResetDTO;
use App\Components\Users\PasswordReset\PasswordResetReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\PasswordReset\Traits\PasswordResetServiceTrait;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Tests\Traits\HelperTrait;
use DateInterval;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use ReflectionException;
use Tests\TestCase;

/**
 * Class PasswordResetServiceTest
 *
 * @package App\Components\Users\Tests\Unit\Service
 */
class PasswordResetServiceTest extends TestCase
{
    use HelperTrait;
    use PasswordResetServiceTrait;
    use AuthServiceTrait;

    /** @var string */
    private string $email = 'demo@example.com';

    /**
     * @return array
     */
    public function correctDataProvider(): array
    {
        return [
            [$this->email, '11223344', '11223344', null],
            [$this->email, '2223344', '11223344', InvalidArgumentException::class],
        ];
    }

    /**
     * @dataProvider correctDataProvider
     *
     * @param string      $email
     * @param string      $newPassword
     * @param string      $passwordRepeat
     * @param null|string $exception
     *
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException|TokenExpiredException|NoResultException
     */
    public function testSuccessCreationAndProcess(
        string $email,
        string $newPassword,
        string $passwordRepeat,
        ?string $exception = null
    ): void {
        $passwordResetDTO = $this->passwordResetService__()->create(
            [
                'email' => $email,
            ]
        )->dto();

        if ($exception !== null) {
            $this->expectException($exception);
        }

        self::assertInstanceOf(PasswordResetReadonlyContract::class, $this->passwordResetService__()->readonly());
        self::assertInstanceOf(PasswordResetDTO::class, $this->passwordResetService__()->dto());
        self::assertEquals(Mutator::TYPE, $passwordResetDTO->_type);

        $this->passwordResetService__()->workWith($passwordResetDTO->id)->setNewPassword(
            [
                'password' => $newPassword,
                'password_repeat' => $passwordRepeat,
            ]
        );

        $this->authService__()->login(new Credentials(new Email($email), new HashedPassword($newPassword)));
    }

    /**
     * @throws BindingResolutionException|NotFoundException|TokenExpiredException
     */
    public function testWorkWithException(): void
    {
        $this->expectException(NotFoundException::class);
        $this->passwordResetService__()->workWith((string)$this->generateIdentity());
    }

    /**
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException|TokenExpiredException|ReflectionException
     */
    public function testExpired(): void
    {
        $this->expectException(TokenExpiredException::class);
        $this->passwordResetService__()->create(
            [
                'email' => $this->email,
            ]
        );

        $passwordReset = $this->getProtectedProperty($this->passwordResetService__(), 'entity');

        $dateTime = new DateTime();
        $dateInterval = new DateInterval('PT1M');
        $dateTime = $dateTime->sub($dateInterval);
        $this->setProtectedProperty($passwordReset, 'ttl', $dateTime);
        $this->setProtectedProperty($this->passwordResetService__(), 'entity', $passwordReset);

        $this->passwordResetService__()->workWith($this->passwordResetService__()->readonly()->identity());
    }

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        $password = $this->randString();

        $this->userService__()->create(
            new Credentials(new Email($this->email), new HashedPassword($password), new HashedPassword($password))
        )->attachProfile(new Payload($this->randString(), $this->randString()));

        $this->setProtectedProperty($this->passwordResetService__(), 'userService__', $this->userService__());
    }
}