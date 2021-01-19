<?php

namespace App\Assistants\Files\ValueObjects;

/**
 * Class DownloadResponseVO
 *
 * @package App\Assistants\Files\ValueObjects
 */
final class DownloadResponseVO
{
    /**
     * @var string
     */
    private string $content;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @param string $content
     * @param array  $headers
     */
    public function __construct(string $content, array $headers)
    {
        $this->content = $content;
        $this->headers = $headers;
    }

    /**
     * @param DownloadResponseVO $file
     *
     * @return bool
     */
    public function equals(DownloadResponseVO $file): bool
    {
        return ($this->content() === $file->content() && $this->headers() === $file->headers());
    }

    /**
     * @return string
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }
}

