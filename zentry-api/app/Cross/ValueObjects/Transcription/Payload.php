<?php

namespace App\Cross\ValueObjects\Transcription;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class Payload
 *
 * @package App\Components\Cross\ValueObjects\Transcription
 */
class Payload
{
    /**
     * @var string
     */
    private string $callbackURL;

    /**
     * @var string
     */
    private string $fallbackURL;

    /**
     * @var string
     */
    private string $resourceURL;

    /**
     * @var bool
     */
    private bool $isRemote;

    /**
     * @param string      $callbackURL
     * @param string      $fallbackURL
     * @param string      $resourceURL
     *
     * @throws RuntimeException
     */
    public function __construct(
        string $callbackURL,
        string $fallbackURL,
        string $resourceURL
    ) {
        if (strEmpty($callbackURL)) {
            throw new InvalidArgumentException('callbackURL cannot be empty');
        }

        if (strEmpty($fallbackURL)) {
            throw new InvalidArgumentException('fallbackURL cannot be empty');
        }

        $this->callbackURL = $callbackURL;
        $this->fallbackURL = $fallbackURL;
        $this->resourceURL = $resourceURL;

        if (filter_var($this->resourceURL, FILTER_VALIDATE_URL)) {
            $this->isRemote = true;
        } else {
            if (!file_exists($this->resourceURL)) {
                throw new RuntimeException("Cannot stat resource at {$this->resourceURL}");
            }

            $this->isRemote = false;
        }
    }

    /**
     * @return string
     */
    public function callbackURL(): string
    {
        return $this->callbackURL;
    }

    /**
     * @return string
     */
    public function fallbackURL(): string
    {
        return $this->fallbackURL;
    }

    /**
     * @return string
     */
    public function resourceURL(): string
    {
        return $this->resourceURL;
    }

    /**
     * @return bool
     */
    public function isRemote(): bool
    {
        return $this->isRemote;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'callback_url' => $this->callbackURL(),
            'fallback_url' => $this->fallbackURL(),
            'resource_url' => $this->resourceURL(),
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)json_encode($this->toArray());
    }
}
