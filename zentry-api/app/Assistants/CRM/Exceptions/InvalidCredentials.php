<?php

namespace App\Assistants\CRM\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class InvalidCredentials
 *
 * @package App\Components\Users\Exceptions\CRM
 */
class InvalidCredentials extends HttpException
{
    /**
     * @param string $message
     * @param int    $code
     */
    public function __construct(
        $message = 'Wrong username or password',
        $code = 422
    ) {
        parent::__construct($code, $message);
    }
}
