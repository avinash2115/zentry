<?php

namespace App\Convention\Services\Traits;

/**
 * Trait FilterableTrait
 *
 * @package App\Convention\Services\Traits
 */
trait FilterableTrait
{
    /**
     * @var array
     */
    private array $filters = [];

    /**
     * @inheritDoc
     */
    public function applyFilters(array $filters): void
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    private function filters(): array
    {
        return $this->filters;
    }
}
