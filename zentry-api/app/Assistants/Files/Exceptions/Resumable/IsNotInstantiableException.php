<?php

namespace App\Assistants\Files\Exceptions\Resumable;

use RuntimeException;
use Throwable;

/**
 * Class IsNotInstantiableException
 *
 * @package App\Assistants\Files\Exceptions\Resumable
 */
class IsNotInstantiableException extends RuntimeException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        string $message = 'Resumable is not instantiable, please check that all parameters have been passed',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
