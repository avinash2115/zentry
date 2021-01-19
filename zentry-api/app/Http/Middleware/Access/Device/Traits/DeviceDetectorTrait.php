<?php

namespace App\Http\Middleware\Access\Device\Traits;

use App\Http\Middleware\Access\Device\Authenticate;
use \Illuminate\Http\Request;
use Illuminate\Routing\Route;
use UnexpectedValueException;

/**
 * Trait DeviceDetectorTrait
 *
 * @package App\Http\Middleware\Access\Device\Traits
 */
trait DeviceDetectorTrait
{
    /**
     * @param Request $request
     *
     * @return bool
     * @throws UnexpectedValueException
     */
    private function devicable(Request $request): bool
    {
        $route = $request->route();

        if (!$route instanceof Route) {
            throw new UnexpectedValueException('Cannot obtain route');
        }

        $middleware = $route->middleware();

        return $request->hasHeader(Authenticate::HEADER) && is_array($middleware) && in_array(Authenticate::ALIAS, $middleware, true);
    }
}
