<?php

namespace App\Assistants\Transformers\JsonApi\Traits;

use App\Convention\Exceptions\Unexpected\PropertyNotInit;

/**
 * Trait IdTrait
 *
 * @package App\Assistants\Transformers\JsonApi\Traits
 */
trait IdTrait
{
    /**
     * @var string
     */
    public string $id;

    /**
     * @return string
     */
    public function id(): string
    {
        if (!is_string($this->id)) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->id;
    }
}
