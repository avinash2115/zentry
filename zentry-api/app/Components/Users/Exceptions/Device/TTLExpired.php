<?php

namespace App\Components\Users\Exceptions\Device;

use RuntimeException;

/**
 * Class TTLExpired
 *
 * @package App\Components\Users\Exceptions\Device
 */
class TTLExpired extends RuntimeException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(
        $message = 'TTL expired',
        $code = 401
    ) {
        parent::__construct($message, $code);
    }
}
