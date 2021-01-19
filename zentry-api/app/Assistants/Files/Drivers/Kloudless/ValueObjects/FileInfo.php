<?php

namespace App\Assistants\Files\Drivers\Kloudless\ValueObjects;

use Arr;
use InvalidArgumentException;

/**
 * Class FileInfo
 *
 * @package App\Assistants\Files\Drivers\Kloudless\ValueObjects
 */
class FileInfo
{
    /**
     * @var array
     */
    private $directories;

    /**
     * @var string
     */
    private $filename;

    /**
     * FileInfo constructor.
     *
     * @param string $path
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $path)
    {
        $pathinfo = pathinfo($path);

        if (!Arr::has($pathinfo, 'extension')) {
            throw new InvalidArgumentException("File name not found at $path");
        }

        $this->filename = Arr::get($pathinfo, 'basename', []);
        $this->directories = explode(DIRECTORY_SEPARATOR, Arr::get($pathinfo, 'dirname', []));
    }

    /**
     * @return string
     */
    public function filename(): string
    {
        return $this->filename;
    }

    /**
     * @return array
     */
    public function directories(): array
    {
        return $this->directories;
    }
}
