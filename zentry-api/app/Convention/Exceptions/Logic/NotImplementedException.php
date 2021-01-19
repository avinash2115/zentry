<?php

namespace App\Convention\Exceptions\Logic;

use LogicException;

/**
 * Class NotImplementedException
 *
 * @package App\Convention\Exceptions\Logic
 */
class NotImplementedException extends LogicException
{
    /**
     * @param string $method
     * @param string $class
     */
    public function __construct(string $method, string $class)
    {
        $message = "Method {$method}() not implemented at {$class}";

        parent::__construct($message, 501, null);
    }
}
