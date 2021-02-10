<?php

namespace App\Assistants\Files\ValueObjects;

/**
 * Class File
 *
 * @package App\Assistants\Files\ValueObjects
 */
final class File
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $url;

    /**
     * @param string $name
     * @param string $url
     */
    public function __construct(string $name, string $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    /**
     * @param File $file
     *
     * @return bool
     */
    public function equals(File $file): bool
    {
        return ($this->name() === $file->name() && $this->url() === $file->url());
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }
}
