<?php

namespace App\Assistants\Files\Exceptions\Resumable\Part;

use Exception;
use Throwable;

/**
 * Class NotFoundException
 *
 * @package App\Assistants\Files\Exceptions\Resumable\Part
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
