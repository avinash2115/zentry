<?php

namespace App\Components\Users\Exceptions\Device;

use RuntimeException;

/**
 * Class TokenExpired
 *
 * @package App\Components\Users\Exceptions\Device
 */
class TokenExpired extends RuntimeException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(
        $message = 'Token expired',
        $code = 401
    ) {
        parent::__construct($message, $code);
    }
}
