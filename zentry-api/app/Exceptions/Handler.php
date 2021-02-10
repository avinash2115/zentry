<?php

namespace App\Exceptions;

use App\Assistants\Files\Exceptions\Resumable\Part\NotFoundException;
use App\Exceptions\JSON\Presenters\Exception as JsonExceptionPresentation;
use Exception;
use Flusher;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class Handler
 *
 * @package App\Exceptions
 */
class Handler extends ExceptionHandler
{
    protected $dontReport = [
        NotFoundException::class
    ];

    /**
     * @inheritdoc
     */
    public function report(Throwable $exception): void
    {
        Flusher::rollback();

        parent::report($exception);
    }

    /**
     * @param Throwable $exception
     *
     * @throws Exception
     */
    public function reportSilent(Throwable $exception): void
    {
        parent::report($exception);
    }

    /**
     * @param Request   $request
     * @param Throwable $exception
     *
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception): Response
    {
        if ($request->ajax() || $request->wantsJson()) {
            $presentedError = new JsonExceptionPresentation($exception, $request);

            return response($presentedError->present(), $presentedError->status());
        }

        return parent::render($request, $exception);
    }
}
