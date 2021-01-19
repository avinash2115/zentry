<?php

namespace App\Components\Sessions\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ActiveException
 *
 * @package App\Convention\Exceptions\Auth
 */
class ActiveException extends HttpException
{
    /**
     * @param string $message
     * @param int    $code
     * @param null   $previous
     */
    public function __construct(
        $message = 'Another active session existed. You should end it, before start the new one.',
        $code = 424,
        $previous = null
    ) {
        parent::__construct($code, $message, $previous);
    }
}
