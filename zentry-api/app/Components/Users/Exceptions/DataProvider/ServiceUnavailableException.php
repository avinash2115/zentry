<?php

namespace App\Components\Users\Exceptions\DataProvider;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ServiceUnavailableException
 *
 * @package App\Components\Users\Exceptions\DataProvider
 */
class ServiceUnavailableException extends HttpException
{
    /**
     * @param string $message
     * @param int    $code
     * @param null   $previous
     */
    public function __construct(
        $message = 'Service is unavailable',
        $code = 0,
        $previous = null
    ) {
        parent::__construct($code, $message, $previous);
    }
}
