<?php

namespace App\Convention\Services\Contracts;

/**
 * Interface FilterableContract
 *
 * @package App\Convention\Services\Contracts
 */
interface FilterableContract
{
    /**
     * @param array $filters
     *
     * @return void
     */
    public function applyFilters(array $filters): void;
}