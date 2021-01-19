<?php

namespace App\Convention\Exceptions\Storage\Dir;

use RuntimeException;
use Throwable;

/**
 * Class CreateException
 *
 * @package App\Convention\Exceptions\Storage\Dir
 */
class CreateException extends RuntimeException
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
