<?php

namespace App\Assistants\Files\Providers\Resumable;

use App\Assistants\Files\Services\Resumable\Validator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Kladislav\LaravelChunkUpload\Handler\HandlerFactory;
use Kladislav\LaravelChunkUpload\ServiceProvider as LaravelChunkUploadServiceProvider;
use Kladislav\LaravelChunkUpload\Storage\ChunkStorage;

/**
 * Class ResumableServiceProvider
 *
 * @package App\Assistants\Files\Providers\Resumable
 */
class ResumableServiceProvider extends LaravelChunkUploadServiceProvider
{
    /**
     */
    public function register(): void
    {
        parent::register();

        $this->app->bind(
            Validator::class,
            static function (Application $app) {
                $request = $app->make(Request::class);

                if ($request->isMethod('GET')) {
                    $file = $request->get('resumableFilename');
                } else {
                    $file = Arr::first($request->allFiles());
                }

                return new Validator(
                    $file,
                    $request,
                    HandlerFactory::classFromRequest($request),
                    $app->make(ChunkStorage::class)
                );
            }
        );
    }
}
