<?php

namespace App\Http\Middleware\Access\JWT;

use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Convention\Exceptions\Auth\UnauthorizedException;
use App\Http\Middleware\Access\Device\Traits\DeviceDetectorTrait;
use Arr;
use Closure;
use Illuminate\Http\Request;

/**
 * Class AuthenticateOrLogin
 *
 * @package App\Http\Middleware\Access\JWT
 */
class AuthenticateOrLogin extends \Tymon\JWTAuth\Http\Middleware\Authenticate
{
    use AuthServiceTrait;

    public const ALIAS = 'jwt-auth-or-login';

    /**
     * @inheritDoc
     */
    public function handle($request, Closure $next)
    {
        if ($request->hasHeader(Authenticate::HEADER)) {
            return parent::handle($request, $next);
        }

        $this->authService__()->login($this->credentials($request));

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return Credentials
     * @throws UnauthorizedException
     */
    private function credentials(Request $request): Credentials
    {
        $data = $request->get('data', []);

        if (!Arr::has($data, 'attributes')) {
            unauthorized();
        }

        $attributes = Arr::get($data, 'attributes', []);

        return new Credentials(
            new Email(Arr::get($attributes, 'email', '')), new HashedPassword(Arr::get($attributes, 'password', '')),
        );
    }
}
