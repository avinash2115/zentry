<?php

namespace App\Assistants\Elastic\Traits;

use App\Assistants\Elastic\Services\ElasticServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait ElasticServiceTrait
 *
 * @package App\Assistants\Elastic\Traits
 */
trait ElasticServiceTrait
{
    /**
     * @var ElasticServiceContract|null
     */
    protected ?ElasticServiceContract $elasticService__ = null;

    /**
     * @return ElasticServiceContract
     * @throws BindingResolutionException
     */
    protected function elasticService__(): ElasticServiceContract
    {
        if (!$this->elasticService__ instanceof ElasticServiceContract) {
            $this->elasticService__ = app()->make(ElasticServiceContract::class);
        }

        return $this->elasticService__;
    }
}
