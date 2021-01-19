<?php

namespace App\Assistants\QR\ValueObjects;

/**
 * Class UrlPayload
 *
 * @package App\Assistants\QR\ValueObjects
 */
final class UrlPayload extends Payload
{
    /**
     * @param Url $url
     */
    public function __construct(Url $url)
    {
        parent::__construct($url->toString());
    }
}