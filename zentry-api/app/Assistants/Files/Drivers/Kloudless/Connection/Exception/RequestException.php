<?php

namespace App\Assistants\Files\Drivers\Kloudless\Connection\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class RequestException
 *
 * @package App\Assistants\Files\Drivers\Kloudless\Connection\Exception
 */
class RequestException extends HttpException
{
    /**
     * RequestException constructor.
     *
     * @param string $message
     * @param int    $code
     */
    public function __construct(int $code, string $message)
    {
        parent::__construct($code, $message);
    }
}
