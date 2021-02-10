<?php

namespace App\Assistants\CRM\Exceptions;

use RuntimeException;

/**
 * Class ConnectionFailed
 *
 * @package App\Components\Users\Exceptions\CRM
 */
class ConnectionFailed extends RuntimeException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(
        $message = '',
        $code = 503
    ) {
        parent::__construct("Connection to CRM service failed, please try again later. Message: {$message}", $code);
    }
}
