<?php

namespace App\Assistants\Elastic\Services\Converter;

use App\Assistants\Common\Filter\ValueObjects\Filter;
use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Aggregation;
use App\Assistants\Elastic\ValueObjects\Filter\Needle;
use App\Assistants\Elastic\ValueObjects\Filter\NeedleCollection;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Mapping;
use App\Assistants\Elastic\ValueObjects\Mappings;
use App\Assistants\Elastic\ValueObjects\Paginator;
use App\Assistants\Elastic\ValueObjects\Sorting;
use App\Assistants\Elastic\ValueObjects\SortingParameters;
use App\Assistants\Search\Services\SearchService;
use App\Assistants\Transformers\ValueObjects\JsonApiResponseBuilder;
use App\Components\Users\Services\Auth\Traits\AuthUserServiceTrait;
use App\Convention\ValueObjects\Meta\Meta;
use App\Convention\ValueObjects\Meta\Pagination;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class ElasticFilterConverter
 *
 * @package App\Assistants\Elastic\Services\Converter
 */
class ElasticFilterConverter
{
    use ElasticServiceTrait;
    use AuthUserServiceTrait;

    /**
     * @var Collection
     */
    private Collection $filter;

    /**
     * @var Index
     */
    private Index $index;

    /**
     * @var IndexableContract
     */
    private IndexableContract $indexable;

    /**
     * @var Mappings
     */
    private Mappings $mappings;

    /**
     * @var Collection
     */
    private Collection $sortBy;

    /**
     * @var null|string
     */
    private ?string $term;

    /**
     * @var Paginator
     */
    private Paginator $paginator;

    /**
     * ElasticFilterConverter constructor.
     *
     * @param Collection        $filter
     * @param Index             $index
     * @param IndexableContract $indexable
     * @param string|null       $term
     *
     * @throws IndexNotSupported
     * @throws BindingResolutionException
     * @throws RuntimeException
     * @throws InvalidArgumentException
     */
    public function __construct(
        Collection $filter,
        Index $index,
        IndexableContract $indexable,
        ?string $term = null
    ) {
        $this->setPaginator($filter);
        $this->filter = $filter;
        $this->index = $index;
        $this->indexable = $indexable;
        $this->setTerm($term);
        $this->mappings = $indexable->asMappings($index);
        $this->setSortBy(app()->make(JsonApiResponseBuilder::class)->sortBy());
    }

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws IndexNotSupported
     */
    public function aggregations(): Collection
    {
        return $this->elasticService__()->aggregations(
            $this->index,
            $this->indexable,
            $this->convertFilterToNeedle($this->filter),
            $this->_term()
        );
    }

    /**
     * @param bool $withCustom
     *
     * @return Collection
     * @throws BindingResolutionException
     */
    public function filter(bool $withCustom = false): Collection
    {
        if (!$this->shouldProcessRequest()) {
            return $this->filter;
        }

        $result = $this->elasticService__()->filter(
            $this->index,
            $this->indexable,
            $this->needleWithFilters(),
            $this->paginator(),
            $this->_term(),
            $this->_sortBy()
        );

        if ($result->isEmpty() && $this->paginator()->enabled()) {
            $pagination = app()->make(Meta::class)->pagination();

            if ($pagination instanceof Pagination && $this->paginator()->page() > $pagination->page()) {
                $result = $this->elasticService__()->filter(
                    $this->index,
                    $this->indexable,
                    $this->needleWithFilters(),
                    new Paginator(
                        $pagination->page(), $this->paginator()->limit()
                    ),
                    $this->_term(),
                    $this->_sortBy()
                );
            }
        }

        return collect(
            [
                'ids' => [
                    'collection' => $result->map(
                        static function (array $result) {
                            return Arr::get($result, '_id');
                        }
                    )->toArray(),
                    'has' => true,
                ],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function filters(
        Collection $aggregations,
        ?Collection $ignoreAttributes = null,
        ?Collection $replaceValueLabels = null,
        ?Collection $replaceFilterLabels = null
    ): Collection {
        if ($ignoreAttributes instanceof Collection) {
            $aggregations = $aggregations->filter(
                static function (Aggregation $aggregation) use ($ignoreAttributes) {
                    return !$ignoreAttributes->has($aggregation->attribute());
                }
            );
        }

        if (!$replaceFilterLabels instanceof Collection) {
            $replaceFilterLabels = collect();
        }

        return $aggregations->map(
            function (Aggregation $aggregation) use ($replaceValueLabels, $replaceFilterLabels) {
                return (new Filter(
                    $aggregation->attribute(),
                    $this->mappings->mapping($aggregation->attribute())->type(),
                    $this->mappings->mappingWeight($aggregation->attribute()),
                    $aggregation->values(),
                    $replaceValueLabels,
                    $replaceFilterLabels->get($aggregation->attribute())
                ))->toArray();
            }
        )->filter()->values();
    }

    /**
     * @return Collection
     */
    public function needle(): Collection
    {
        $mappingsByAttribute = $this->mappings->collection()->keyBy(
            static function (Mapping $mapping) {
                return $mapping->attribute();
            }
        );

        return collect($this->filter->get('elastic'))->map(
            function ($value, $attribute) use ($mappingsByAttribute) {
                if ($mappingsByAttribute->has($attribute)) {
                    return $this->createNeedle($attribute, $value);
                }

                return null;
            }
        )->filter();
    }

    /**
     * @return Collection
     * @todo remove if it doesn' used
     */
    public function needleWithFilters(): Collection
    {
        return $this->needle()->merge($this->convertFilterToNeedle($this->filter));
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return Needle
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    private function createNeedle($attribute, $value): Needle
    {
        return new Needle($attribute, $value, $this->mappings);
    }

    /**
     * @return null|string
     */
    private function _term(): ?string
    {
        return $this->term;
    }

    /**
     * @return Collection
     */
    private function _sortBy(): Collection
    {
        return $this->sortBy;
    }

    /**
     * @param null|string $term
     *
     * @return ElasticFilterConverter
     */
    private function setTerm(?string $term = null): ElasticFilterConverter
    {
        if ($term !== null) {
            $term = SearchService::sanitize($term);
        }

        $this->term = $term;

        return $this;
    }

    /**
     * @param Collection $sortBy
     *
     * @return ElasticFilterConverter
     */
    private function setSortBy(Collection $sortBy): ElasticFilterConverter
    {
        if (!$this->shouldProcessRequest() || !in_array($this->indexable->asType()->type(), Sorting::SHOULD_BE_SORTED, true)) {
            $this->sortBy = collect();

            return $this;
        }

        $this->sortBy = collect($sortBy->get($this->indexable->asType()->type(), []))->map(
            function ($subject, $key) {
                $sortParameters = $this->sorting($subject, $key);

                return new Sorting($this->mappings->mapping($sortParameters->key()), $sortParameters->direction());
            }
        )->filter()->values();

        return $this;
    }

    /**
     * @param mixed $data
     * @param mixed $key
     *
     * @return SortingParameters
     * @throws InvalidArgumentException
     */
    private function sorting($data, $key): SortingParameters
    {
        if (is_array($data)) {
            $route = Sorting::ASC;
            foreach ($data as $subject => $value) {
                $sorting = $this->sorting($value, $subject);
                if (!is_int($key)) {
                    $key .= "_{$sorting->key()}";
                } else {
                    $key = $sorting->key();
                }

                $route = $sorting->direction();
            }

            return new SortingParameters($key, $route);
        }

        $subject = (string)$data;

        $route = strpos($subject, "-") === false ? Sorting::ASC : Sorting::DESC;
        $subject = str_replace("-", "", $subject);
        if (!is_int($key)) {
            $key .= $subject;
        } else {
            $key = $subject;
        }

        return new SortingParameters($key, $route);
    }

    /**
     * @param Collection $valueAttributes
     * @param string     $searchType
     *
     * @return NeedleCollection
     * @throws InvalidArgumentException
     */
    private function createNeedleCollection(
        Collection $valueAttributes,
        string $searchType = NeedleCollection::SHOULD
    ): NeedleCollection {
        return new NeedleCollection($valueAttributes, $searchType);
    }

    /**
     * @param Collection $elasticFilter
     *
     * @return ElasticFilterConverter
     */
    private function setPaginator(Collection $elasticFilter): ElasticFilterConverter
    {
        $page = 0;
        $limit = -1;

        if ($elasticFilter->has(Paginator::PARAMETER)) {
            $parameters = $elasticFilter->get(Paginator::PARAMETER);
            if (Arr::has($parameters, 'page') && Arr::has($parameters, 'limit')) {
                $page = Arr::get($parameters, 'page', 0);
                $limit = Arr::get($parameters, 'limit', 0);
            }
            $elasticFilter->forget(Paginator::PARAMETER);
        }

        $this->paginator = new Paginator($page, $limit);

        return $this;
    }

    /**
     * @return Paginator
     */
    public function paginator(): Paginator
    {
        return $this->paginator;
    }



    /**
     * @param Collection $filter
     *
     * @return Collection
     * @todo separate requestFilter\controller filters or make it more obvious
     */
    public function convertFilterToNeedle(Collection $filter): Collection
    {
        return $filter->map(
            function ($value, $attribute) {
                switch ($attribute) {
                    case '_id':
                        return new Needle($attribute, $value, new Mappings(collect([new Mapping($attribute, Mapping::NEEDLE_TYPE_IDENTIFIER)])));
                    case 'user_id':
                        return $this->createNeedle($attribute, $value);
                    default:
                        return null;
                }
            }
        )->filter();
    }

    /**
     * @param bool $withCustom
     *
     * @return bool
     */
    public function shouldProcessRequest(bool $withCustom = false): bool
    {
        return !app()->runningUnitTests() && ($withCustom || $this->_term() !== null || $this->needle()->isNotEmpty() || $this->paginator()->enabled());
    }
}
