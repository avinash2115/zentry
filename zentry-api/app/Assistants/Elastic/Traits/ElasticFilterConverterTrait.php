<?php

namespace App\Assistants\Elastic\Traits;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Services\Converter\ElasticFilterConverter;
use App\Assistants\Elastic\ValueObjects\Index;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Trait ElasticFilterConverterTrait
 *
 * @see     ElasticServiceTrait
 * @package App\Assistants\Elastic\Traits
 */
trait ElasticFilterConverterTrait
{
    /**
     * @var ElasticFilterConverter
     */
    protected ElasticFilterConverter $elasticFilterConverter__;

    /**
     * @param Collection        $filter
     * @param Index             $index
     * @param IndexableContract $indexable
     * @param null|string       $term
     *
     * @return ElasticFilterConverter
     * @throws IndexNotSupported
     * @throws BindingResolutionException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    protected function elasticFilterConverter__(
        Collection $filter,
        Index $index,
        IndexableContract $indexable,
        ?string $term = null
    ): ElasticFilterConverter {
        $this->elasticFilterConverter__ = new ElasticFilterConverter($filter, $index, $indexable, $term);

        return $this->elasticFilterConverter__;
    }
}
