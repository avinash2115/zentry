<?php

namespace App\Assistants\Elastic\Console\Generators\Base\Traits;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Index as ElasticIndex;

/**
 * Trait IndexableTrait
 *
 * @package App\Assistants\Elastic\Console\Generators\Base\Traits
 */
trait IndexableTrait
{
    use ElasticServiceTrait;

    /**
     * @param IndexableContract $indexable
     */
    private function index(IndexableContract $indexable): void
    {
        collect(ElasticIndex::AVAILABLE_INDEXES)->each(function (string $index) use ($indexable){
            try {
                $this->elasticService__()
                    ->index($this->elasticService__()::generateIndex($index), $indexable, false);
            } catch (IndexNotSupported $exception) {
            }
        });
    }
}
