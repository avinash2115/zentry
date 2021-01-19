<?php

namespace App\Assistants\Search\Agency\Services;

use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Interface Searchable
 *
 * @package App\Assistants\Search\Agency\Services
 */
interface Searchable
{
    /**
     * Should return collection of PresenterContract
     *
     * @param Collection      $results
     * @param Collection|null $filters
     *
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function verifyResults(Collection $results, ?Collection $filters = null): Collection;

    /**
     * Should return filtered collection within elements which passes a validation
     *
     * @param Collection $suggestions
     *
     * @return bool
     */
    public function verifyAutocomplete(Collection $suggestions): bool;
}
