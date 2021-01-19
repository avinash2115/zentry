<?php

namespace App\Convention\Exceptions\Repository;

use Exception;
use Throwable;

/**
 * Class NotFoundException
 *
 * @package App\Convention\Exceptions\Repository
 */
class NotFoundException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(string $message = 'Not Found Exception', int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}
