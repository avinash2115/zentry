<?php

namespace App\Components\Users\Services\Auth;

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Components\Users\ValueObjects\SSO\Driver;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Services\Doctrine\Facades\Flusher;
use App\Http\Middleware\Access\Device\Authenticate;
use Auth;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use JWTAuth;
use Request;
use UnexpectedValueException;

/**
 * Class AuthService
 *
 * @package App\Components\Users\Services\Auth
 */
class AuthService implements AuthServiceContract
{
    use UserServiceTrait;

    public const REMEMBER_TOKEN = true;

    /**
     * @inheritDoc
     */
    public function signup(Credentials $credentials, Payload $payload): ?string
    {
        $this->checkCredential($credentials);

        $this->userService__()->create($credentials)->attachProfile($payload);

        Flusher::flush();

        return $this->login($credentials);
    }

    /**
     * @param Credentials $credential
     *
     * @throws InvalidArgumentException
     */
    private function checkCredential(Credentials $credential): void
    {
        if (!$credential->passwordRepeat() instanceof HashedPassword || !$credential->password()->equals(
                $credential->passwordRepeat()
            )) {
            throw new InvalidArgumentException('Password must equals repeat password');
        }
    }

    /**
     * @inheritDoc
     */
    public function login(Credentials $credentials): ?string
    {
        if ($credentials->remember()) {
            JWTAuth::factory()->setTTL(config('jwt.ttl_remember_me'));
        } else {
            JWTAuth::factory()->setTTL(config('jwt.ttl'));
        }

        $token = Auth::attempt($credentials->toArray());

        if ($credentials->devicePayload() instanceof ConnectingPayload) {
            $this->user()->connect($credentials->devicePayload());
        }

        return is_string($token) ? $token : null;
    }

    /**
     * @inheritDoc
     */
    public function loginOnceFromUser(UserReadonlyContract $user): bool
    {
        return Auth::onceUsingId($user->identity());
    }

    /**
     * @inheritDoc
     */
    public function tokenFromUser(UserReadonlyContract $user): string
    {
        return Auth::tokenById($user->identity());
    }

    /**
     * @inheritDoc
     */
    public function logout(): void
    {
        Auth::logout();
    }

    /**
     * @inheritDoc
     */
    public function user(): AuthUserServiceContract
    {
        if (!$this->check()) {
            throw new NotFoundException('User not found');
        }

        $user = Auth::user();

        if (!$user instanceof AuthUserServiceContract) {
            throw new UnexpectedValueException('User must be instance of AuthUserServiceContract');
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function deviceReference(): ?string
    {
        $this->user();
        $reference = Request::header(Authenticate::HEADER);

        return is_string($reference) ? $reference : null;
    }

    /**
     * @inheritDoc
     */
    public function check(): bool
    {
        return Auth::check();
    }

    /**
     * @inheritDoc
     */
    public function drivers(): Collection
    {
        return collect(config('users.sso.drivers'))->map(
            function (array $values) {
                return new Driver(...array_values($values));
            }
        );
    }
}
