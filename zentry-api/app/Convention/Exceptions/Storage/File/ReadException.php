<?php

namespace App\Convention\Exceptions\Storage\File;

use RuntimeException;
use Throwable;

/**
 * Class ReadException
 *
 * @package App\Convention\Exceptions\Storage\File
 */
class ReadException extends RuntimeException
{
    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 500, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
