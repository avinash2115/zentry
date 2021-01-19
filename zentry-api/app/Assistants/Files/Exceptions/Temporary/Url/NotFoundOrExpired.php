<?php

namespace App\Assistants\Files\Exceptions\Temporary\Url;

use RuntimeException;
use Throwable;

/**
 * Class NotFoundOrExpired
 *
 * @package App\Assistants\Files\Exceptions\Temporary\Url
 */
class NotFoundOrExpired extends RuntimeException
{
    /**
     * @inheritDoc
     */
    public function __construct(
        string $message = 'Temporary url not found or expired',
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}