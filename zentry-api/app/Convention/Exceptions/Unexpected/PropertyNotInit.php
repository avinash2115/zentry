<?php

namespace App\Convention\Exceptions\Unexpected;

use UnexpectedValueException;

/**
 * Class PropertyNotInit
 *
 * @package App\Convention\Exceptions\Unexpected
 */
class PropertyNotInit extends UnexpectedValueException
{
    /**
     * @param string $method
     * @param string $class
     */
    public function __construct(string $method, string $class)
    {
        $message = "Cannot get property at {$method}() {$class}";

        parent::__construct($message, 500, null);
    }
}
