<?php

namespace App\Components\Users\Exceptions\ResetPassword;

use ErrorException;

/**
 * Class TokenExpiredException
 *
 * @package App\Components\Users\Exceptions\ResetPassword
 */
class TokenExpiredException extends ErrorException
{
    /**
     * @param string $message
     * @param int    $code
     * @param int    $severity
     */
    public function __construct(
        $message = '',
        $code = 0,
        $severity = 1
    ) {
        parent::__construct($message, $code, $severity);
    }
}
