<?php

namespace App\Assistants\Elastic\Services\Setup\Traits;

use App\Assistants\Elastic\Services\Setup\FilterServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait FilterServiceTrait
 *
 * @package App\Assistants\Elastic\Services\Setup\Traits
 */
trait FilterServiceTrait
{
    /**
     * @var FilterServiceContract | null
     */
    protected ?FilterServiceContract $filterService__ = null;

    /**
     * @return FilterServiceContract
     * @throws BindingResolutionException
     */
    protected function filterService__(): FilterServiceContract
    {
        if (!$this->filterService__ instanceof FilterServiceContract) {
            $this->filterService__ = app()->make(FilterServiceContract::class);
        }

        return $this->filterService__;
    }
}
