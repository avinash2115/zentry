<?php

namespace App\Convention\Exceptions\Permit;

use ErrorException;

/**
 * Class PermissionDeniedException
 *
 * @package App\Convention\Exceptions\Permit
 */
class PermissionDeniedException extends ErrorException
{
    /**
     * @param string $message
     * @param int    $code
     * @param int    $severity
     * @param string $filename
     * @param int    $lineno
     * @param null   $previous
     */
    public function __construct(
        $message = '',
        $code = 403,
        $severity = 1,
        $filename = __FILE__,
        $lineno = __LINE__,
        $previous = null
    ) {
        parent::__construct($message, $code, $severity, $filename, $lineno, $previous);
    }
}
