<?php

namespace App\Assistants\Files\ValueObjects;

use Arr;

/**
 * Class Metadata
 *
 * @package App\Assistants\Files\ValueObjects
 */
final class Metadata
{
    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $path;

    /**
     * @var int
     */
    private int $timestamp;

    /**
     * @var int
     */
    private int $size;

    /**
     * @param array $metadata
     */
    public function __construct(array $metadata)
    {
        $this->type = Arr::get($metadata, 'type', '');
        $this->path = Arr::get($metadata, 'path', '');
        $this->timestamp = Arr::get($metadata, 'timestamp', 0);
        $this->size = Arr::get($metadata, 'size', 0);
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function timestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function size(): int
    {
        return $this->size;
    }
}
