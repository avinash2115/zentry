<?php

namespace App\Assistants\CRM\Exceptions;

use RuntimeException;

/**
 * Class UndefinedCRMDriver
 *
 * @package App\Components\Users\Exceptions\CRM
 */
class UndefinedCRMDriver extends RuntimeException
{
    /**
     * @param string $driver
     * @param int    $code
     */
    public function __construct(
        $driver,
        $code = 500
    ) {
        parent::__construct("Undefined CRM driver: {$driver}", $code);
    }
}
