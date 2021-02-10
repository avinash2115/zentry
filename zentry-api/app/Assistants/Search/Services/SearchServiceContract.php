<?php

namespace App\Assistants\Search\Services;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Search\Search\SearchDTO;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

/**
 * Interface SearchServiceContract
 *
 * @package App\Assistants\Search\Services
 */
interface SearchServiceContract
{
    /**Elastic/Console/Base/Indexing/Index.php
     * @return bool
     * @throws BindingResolutionException
     */
    public function setup(): bool;

    /**
     * @param IndexableContract $indexable
     *
     * @return SearchServiceContract
     */
    public function directly(IndexableContract $indexable): SearchServiceContract;

    /**
     * @return SearchServiceContract
     */
    public function globally(): SearchServiceContract;

    /**
     * @param string $term
     *
     * @return SearchDTO
     * @throws BindingResolutionException
     */
    public function search(string $term): SearchDTO;

    /**
     * @param string          $term
     * @param Collection|null $needle
     *
     * @return Collection
     * @throws BindingResolutionException
     */
    public function autocomplete(string $term, ?Collection $needle = null): Collection;
}
