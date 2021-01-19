<?php

namespace App\Assistants\Files\Drivers\Local;

use App\Assistants\Files\Drivers\Contracts\Quotable;
use App\Assistants\Files\Drivers\ValueObjects\Quota;
use League\Flysystem\Adapter\Local;

/**
 * Class Adapter
 *
 * @package App\Assistants\Files\Drivers\Local
 */
class Adapter extends Local implements Quotable
{
    /**
     * @inheritDoc
     */
    public function quota(string $path = null): Quota
    {
        return new Quota($this->dirSize($this->applyPathPrefix($path)), 0);
    }

    /**
     * @param string $path
     *
     * @return int
     */
    private function dirSize(string $path): int
    {
        $size = 0;

        $content = glob(rtrim($path, '/').'/*', GLOB_NOSORT);

        if ($content === false) {
            return $size;
        }

        foreach ($content as $item) {
            $size += is_file($item) ? filesize($item) : $this->dirSize($item);
        }

        return $size;
    }
}
