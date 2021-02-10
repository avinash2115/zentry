<?php

namespace App\Convention\Exceptions\Auth;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class WrongCredentials
 *
 * @package App\Convention\Exceptions\Auth
 */
class WrongCredentials extends HttpException
{
    /**
     * @param string $message
     * @param int    $code
     * @param null   $previous
     */
    public function __construct(
        $message = 'Wrong Credentials',
        $code = 422,
        $previous = null
    ) {
        parent::__construct($code, $message, $previous);
    }
}
