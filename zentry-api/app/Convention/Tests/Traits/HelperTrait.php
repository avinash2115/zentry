<?php

namespace App\Convention\Tests\Traits;

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\Profile\ProfileContract;
use App\Components\Users\User\Repository\UserRepositoryContract;
use App\Components\Users\User\Repository\UserRepositoryDoctrine;
use App\Components\Users\User\UserContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Auth;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Trait HelperTrait
 */
trait HelperTrait
{
    use UserServiceTrait;

    /**
     * @var UserRepositoryDoctrine|null
     */
    private ?UserRepositoryDoctrine $userRepository = null;

    /**
     * Get rand string
     *
     * @param int $length
     *
     * @return string
     * @throws Exception
     */
    private function randString(int $length = 16): string
    {
        return Str::random($length);
    }

    /**
     * @param int $length
     *
     * @return string
     */
    private function randEmail(int $length = 8): string
    {
        return Str::random($length) . '@test.com';
    }

    /**
     * Get rand int
     *
     * @return int
     * @throws Exception
     */
    private function randInt(): int
    {
        return random_int(1, 1000);
    }

    /**
     * Generate identity
     *
     * @return Identity
     */
    private function generateIdentity(): Identity
    {
        return IdentityGenerator::next();
    }

    /**
     * @param Email|null          $email
     * @param HashedPassword|null $password
     *
     * @return UserContract
     * @throws BindingResolutionException
     * @throws Exception
     */
    private function createUser(Email $email = null, HashedPassword $password = null): UserContract
    {
        $credentials = new Credentials(
            $email instanceof Email ? $email : new Email($this->randEmail()),
            $password instanceof HashedPassword ? $password : new HashedPassword($this->randString())
        );

        $user = app()->make(
            UserContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'credentials' => $credentials,
            ]
        );

        $user->attachProfile(
            app()->make(
                ProfileContract::class,
                [
                    'identity' => IdentityGenerator::next(),
                    'user' => $user,
                    'payload' => new Payload($this->randString(), $this->randString()),
                ]
            )
        );

        return $user;
    }

    /**
     * @param Identity|null $identity
     *
     * @throws BindingResolutionException
     * @throws NoResultException
     * @throws NonUniqueResultException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws Exception
     */
    private function login(Identity $identity = null): void
    {
        if ($identity === null) {
            $this->userService__()->create($this->credentials());
            $this->userService__()->attachProfile(new Payload($this->randString(), $this->randString()));
            $identity = $this->userService__()->readonly()->identity();
        }

        Auth::byId($identity);
    }

    /**
     * @param string|null $email
     * @param string|null $password
     *
     * @return Credentials
     * @throws Exception
     */
    private function credentials(string $email = null, string $password = null): Credentials
    {
        if ($email === null) {
            $email = $this->randString(5) . '@test.com';
        }

        if ($password === null) {
            $password = $this->randString();
        }

        return new Credentials(new Email($email), new HashedPassword($password));
    }

    /**
     * @return UserReadonlyContract
     * @throws BindingResolutionException
     */
    private function createPersistedUser(): UserReadonlyContract
    {
        $user = $this->createUser();

        $this->userRepository()->persist($user);

        return $user;
    }

    /**
     * @return UserRepositoryContract
     * @throws BindingResolutionException
     */
    protected function userRepository(): UserRepositoryContract
    {
        if (!$this->userRepository instanceof UserRepositoryContract) {
            $this->setUserRepository();
        }

        return $this->userRepository;
    }

    /**
     * @throws BindingResolutionException
     */
    private function setUserRepository(): void
    {
        $this->userRepository = app()->make(UserRepositoryDoctrine::class);
    }

    /**
     * @param string $name
     * @param string $domain
     *
     * @return string
     */
    private function email(string $name, string $domain = 'example.com'): string
    {
        return "$name@$domain";
    }
}
