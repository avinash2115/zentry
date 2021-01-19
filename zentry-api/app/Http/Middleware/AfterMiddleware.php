<?php

namespace App\Http\Middleware;

use Closure;
use Flusher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class AfterMiddleware
 *
 * @package App\Http\Middleware
 */
class AfterMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Flusher::open();

        $response = $next($request);

        if ($response instanceof Response && $response->isSuccessful()) {
            Flusher::flush();
            Flusher::commit();
        } else {
            Flusher::rollback();
        }

        return $response;
    }
}
