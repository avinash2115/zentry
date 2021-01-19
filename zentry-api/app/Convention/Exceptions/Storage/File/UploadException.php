<?php

namespace App\Convention\Exceptions\Storage\File;

use RuntimeException;
use Throwable;

/**
 * Class UploadException
 *
 * @package App\Convention\Exceptions\Storage\File
 */
class UploadException extends RuntimeException
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
