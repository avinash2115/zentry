<?php

namespace App\Components\Users\Exceptions\DataProvider\Auth;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class TokenExpired
 *
 * @package App\Convention\Exceptions\Auth
 */
class TokenExpired extends HttpException
{
    /**
     * @param string $message
     * @param int    $code
     * @param null   $previous
     */
    public function __construct(
        $message = 'Token Expired',
        $code = 0,
        $previous = null
    ) {
        parent::__construct($code, $message, $previous);
    }
}
