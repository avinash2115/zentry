<?php

namespace App\Assistants\Elastic\Exceptions;

use App\Assistants\Elastic\ValueObjects\Index;
use InvalidArgumentException;

/**
 * Class IndexNotSupported
 *
 * @package App\Assistants\Elastic\Exceptions
 */
class IndexNotSupported extends InvalidArgumentException
{
    /**
     * @param Index $index
     */
    public function __construct(Index $index)
    {
        parent::__construct("{$index->index()} is not supported");
    }
}