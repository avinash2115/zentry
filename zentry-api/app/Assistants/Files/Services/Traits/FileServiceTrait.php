<?php

namespace App\Assistants\Files\Services\Traits;

use App\Assistants\Files\Services\FileServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException as BindingResolutionExceptionAlias;
use Illuminate\Support\Facades\Storage;

/**
 * Trait FileServiceTrait
 *
 * @package App\Assistants\Files\Services\Traits
 */
trait FileServiceTrait
{
    /**
     * @var FileServiceContract | null
     */
    private ?FileServiceContract $fileService__ = null;

    /**
     * @return FileServiceContract
     * @throws BindingResolutionExceptionAlias
     */
    private function fileService__(): FileServiceContract
    {
        if (!$this->fileService__ instanceof FileServiceContract) {
            return $this->setFileService__();
        }

        return $this->fileService__;
    }

    /**
     * @throws BindingResolutionExceptionAlias
     */
    private function setFileService__(): FileServiceContract
    {
        $this->fileService__ = app()->make(
            FileServiceContract::class,
            [
                'storage' => Storage::disk(config('filesystems.default')),
            ]
        );

        return $this->fileService__;
    }
}
