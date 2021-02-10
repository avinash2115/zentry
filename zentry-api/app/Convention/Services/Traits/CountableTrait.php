<?php

namespace App\Convention\Services\Traits;

/**
 * Trait CountableTrait
 *
 * @package App\Convention\Services\Traits
 */
trait CountableTrait
{
    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $this->handleFilters($this->filters());

        $count = $this->_repository()->count();

        $this->applyFilters([]);

        return $count;
    }
}
