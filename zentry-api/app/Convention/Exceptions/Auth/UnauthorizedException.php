<?php

namespace App\Convention\Exceptions\Auth;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class UnauthorizedException
 *
 * @package App\Convention\Exceptions\Auth
 */
class UnauthorizedException extends HttpException
{
    /**
     * @param string $message
     * @param int    $code
     * @param null   $previous
     */
    public function __construct(
        $message = 'Access Denied',
        $code = 401,
        $previous = null
    ) {
        parent::__construct($code, $message, $previous);
    }
}
