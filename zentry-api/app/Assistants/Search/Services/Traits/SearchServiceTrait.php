<?php

namespace App\Assistants\Search\Services\Traits;

use App\Assistants\Search\Services\SearchServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait SearchServiceTrait
 *
 * @package App\Assistants\Search\Services\Traits
 */
trait SearchServiceTrait
{
    /**
     * @var SearchServiceContract|null
     */
    protected ?SearchServiceContract $searchService__ = null;

    /**
     * @return SearchServiceContract
     * @throws BindingResolutionException
     */
    protected function searchService__(): SearchServiceContract
    {
        if (!$this->searchService__ instanceof SearchServiceContract) {
            $this->searchService__ = app()->make(SearchServiceContract::class);
        }

        return $this->searchService__;
    }
}
