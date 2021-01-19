<?php

namespace App\Assistants\Files\Services\Contracts;

use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use UnexpectedValueException;

/**
 * Interface HasFiles
 */
interface HasFiles extends AsFileNamespace
{
    /**
     * @param string $filename
     *
     * @return string
     */
    public function filePath(string $filename): string;

    /**
     * @param bool $humanReadable
     *
     * @return array
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    public function fileNamespaceParts(bool $humanReadable = false): array;
}
