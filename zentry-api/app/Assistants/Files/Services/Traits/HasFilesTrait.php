<?php

namespace App\Assistants\Files\Services\Traits;

use App\Assistants\Files\Services\Contracts\HasFiles;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Helpers\FileUtility;
use InvalidArgumentException;
use RuntimeException;

/**
 * Trait HasFilesTrait
 *
 * @package App\Assistants\Files\Services\Traits
 */
trait HasFilesTrait
{
    /**
     * @inheritDoc
     */
    public function filePath(string $filename): string
    {
        return $this->fileNamespace() . DIRECTORY_SEPARATOR . FileUtility::sanitizeName($filename);
    }

    /**
     * @inheritDoc
     */
    public function fileNamespace(bool $humanReadable = false): string
    {
        if ($this instanceof HasFiles) {
            $parts = array_map(
                static function (string $part) {
                    return FileUtility::sanitizeName($part);
                },
                $this->fileNamespaceParts($humanReadable)
            );

            return implode(DIRECTORY_SEPARATOR, $parts);
        }

        throw new RuntimeException('Class must implement HasFiles contract');
    }
}
