<?php

namespace App\Assistants\Files\Services\Traits;

use App\Assistants\Files\Services\FileServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException as BindingResolutionExceptionAlias;
use Illuminate\Support\Facades\Storage;

/**
 * Trait LocalFileServiceTrait
 *
 * @package App\Assistants\Files\Services\Traits
 */
trait LocalFileServiceTrait
{
    /**
     * @var FileServiceContract | null
     */
    private ?FileServiceContract $localFileService__ = null;

    /**
     * @return FileServiceContract
     * @throws BindingResolutionExceptionAlias
     */
    private function localFileService__(): FileServiceContract
    {
        if (!$this->localFileService__ instanceof FileServiceContract) {
            return $this->setLocalFileService__();
        }

        return $this->localFileService__;
    }

    /**
     * @throws BindingResolutionExceptionAlias
     */
    private function setLocalFileService__(): FileServiceContract
    {
        $this->localFileService__ = app()->make(
            FileServiceContract::class,
            [
                'storage' => Storage::disk('local'),
            ]
        );

        return $this->localFileService__;
    }
}
