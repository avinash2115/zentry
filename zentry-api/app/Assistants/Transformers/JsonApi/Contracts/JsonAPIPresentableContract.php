<?php

namespace App\Assistants\Transformers\JsonApi\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface JsonAPIPresentableContract
 *
 * @package App\Assistants\Transformers\JsonApi\Contracts
 */
interface JsonAPIPresentableContract
{
    /**
     * @return Collection
     */
    public function present(): Collection;

    /**
     * @return bool
     */
    public function isEmpty(): bool;
}
