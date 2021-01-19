<?php

namespace App\Convention\Exceptions\Storage;

use RuntimeException;
use Throwable;

/**
 * Class StreamNotSupportedException
 *
 * @package App\Convention\Exceptions\Storage
 */
class StreamNotSupportedException extends RuntimeException
{
    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}