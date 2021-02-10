<?php

namespace App\Http\Middleware\Access\Shared\Traits;

use App\Http\Middleware\Access\Shared\Authenticate;
use \Illuminate\Http\Request;
use Illuminate\Routing\Route;
use UnexpectedValueException;

/**
 * Trait SharedDetectorTrait
 *
 * @package App\Http\Middleware\Access\Shared\Traits
 */
trait SharedDetectorTrait
{
    /**
     * @param Request $request
     *
     * @return bool
     * @throws UnexpectedValueException
     */
    private function sharable(Request $request): bool
    {
        $route = $request->route();

        if (!$route instanceof Route) {
            throw new UnexpectedValueException('Cannot obtain route');
        }

        $middleware = $route->middleware();

        return $request->hasHeader(Authenticate::HEADER) && is_array($middleware) && in_array(Authenticate::ALIAS, $middleware, true);
    }
}
