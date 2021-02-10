<?php

namespace App\Assistants\Search\Agency\Services\Traits;

use Illuminate\Support\Collection;

/**
 * Trait SearchableTrait
 *
 * @package App\Assistants\Search\Agency\Services
 */
trait SearchableTrait
{
    /**
     * @inheritDoc
     */
    public function verifyAutocomplete(Collection $suggestions): bool
    {
        return $this->verifyResults($suggestions, collect(['limit' => 1]))->isNotEmpty();
    }
}
