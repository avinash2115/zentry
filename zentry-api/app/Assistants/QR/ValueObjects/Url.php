<?php

namespace App\Assistants\QR\ValueObjects;

use InvalidArgumentException;

/**
 * Class Url
 *
 * @package App\Assistants\QR\ValueObjects
 */
final class Url
{
    /**
     * @var string
     */
    private string $url;

    /**
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->setUrl($url);
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param string $url
     *
     * @return Url
     */
    private function setUrl(string $url): Url
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Url is not valid');
        }

        $this->url = $url;

        return $this;
    }
}