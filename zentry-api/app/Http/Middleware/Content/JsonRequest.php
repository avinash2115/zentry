<?php

namespace App\Http\Middleware\Content;

use Closure;
use Illuminate\Http\Request;
use InvalidArgumentException;
use JsonTransformer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

/**
 * Class JsonRequest
 *
 * @package App\Http\Middleware\Content
 */
class JsonRequest
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     * @throws Throwable
     */
    public function handle($request, Closure $next)
    {
        if ($request->wantsJson()) {
            $request->merge(['processed' => true]);

            $response = $next(JsonTransformer::from($request));

            $headerContentDisposition = $response->headers->get('content-disposition');

            if (is_array($headerContentDisposition)) {
                $headerContentDisposition = implode("", $headerContentDisposition);
            }

            if (strpos(
                    (string)$headerContentDisposition,
                    'attachment'
                ) !== false || $response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
                return $response;
            }

            return $response->setContent(
                JsonTransformer::to($response->original)
            );
        }

        throw new InvalidArgumentException('Invalid content type request');
    }
}
