<?php

namespace App\Assistants\Files\Services\Contracts;

use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use InvalidArgumentException;

/**
 * Interface AsFileNamespace
 */
interface AsFileNamespace
{
    /**
     * @param bool $humanReadable
     *
     * @return string
     */
    public function fileNamespace(bool $humanReadable = false): string;
}
