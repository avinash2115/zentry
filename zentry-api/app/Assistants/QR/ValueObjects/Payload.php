<?php

namespace App\Assistants\QR\ValueObjects;

/**
 * Class Payload
 *
 * @package App\Assistants\QR\ValueObjects
 */
class Payload
{
    /**
     * @var string
     */
    protected string $text;

    /**
     * @param string $text
     */
    public function __construct(string $text)
    {
        $this->setText($text);
    }

    /**
     * @param string $text
     *
     * @return Payload
     */
    final private function setText(string $text): Payload
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    final public function text(): string
    {
        return $this->text;
    }
}