<?php

namespace App\Http\Middleware\Access\JWT;

use App\Http\Middleware\Access\Device\Traits\DeviceDetectorTrait;
use App\Http\Middleware\Access\Shared\Traits\SharedDetectorTrait;
use Closure;

/**
 * Class Authenticate
 *
 * @package App\Http\Middleware\Access\JWT
 */
class Authenticate extends \Tymon\JWTAuth\Http\Middleware\Authenticate
{
    use DeviceDetectorTrait;
    use SharedDetectorTrait;

    public const HEADER = 'Authorization';

    public const ALIAS = 'jwt-auth';

    /**
     * @inheritDoc
     */
    public function handle($request, Closure $next)
    {
        if ($this->devicable($request) || $this->sharable($request)) {
            return $next($request);
        }

        return parent::handle($request, $next);
    }
}
