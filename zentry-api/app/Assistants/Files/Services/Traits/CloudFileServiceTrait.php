<?php

namespace App\Assistants\Files\Services\Traits;

use App\Assistants\Files\Services\FileServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException as BindingResolutionExceptionAlias;
use Illuminate\Support\Facades\Storage;

/**
 * Trait CloudFileServiceTrait
 *
 * @package App\Assistants\Files\Services\Traits
 */
trait CloudFileServiceTrait
{
    /**
     * @var FileServiceContract | null
     */
    private ?FileServiceContract $cloudFileService__ = null;

    /**
     * @return FileServiceContract
     * @throws BindingResolutionExceptionAlias
     */
    private function cloudFileService__(): FileServiceContract
    {
        if (!$this->cloudFileService__ instanceof FileServiceContract) {
            return $this->setCloudFileService__();
        }

        return $this->cloudFileService__;
    }

    /**
     * @throws BindingResolutionExceptionAlias
     */
    private function setCloudFileService__(): FileServiceContract
    {
        $this->cloudFileService__ = app()->make(
            FileServiceContract::class,
            [
                'storage' => Storage::cloud()
            ]
        );

        return $this->cloudFileService__;
    }
}
