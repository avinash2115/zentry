<?php

namespace App\Assistants\Elastic\Services;

use App\Assistants\Elastic\Contracts\Indexable\SetupableContract;
use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Elastic\ValueObjects\Aggregation;
use App\Assistants\Elastic\ValueObjects\Body;
use App\Assistants\Elastic\ValueObjects\Document;
use App\Assistants\Elastic\ValueObjects\Filter\Contracts\NeedlePresenter;
use App\Assistants\Elastic\ValueObjects\Filter\Needle;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Mapping;
use App\Assistants\Elastic\ValueObjects\Paginator;
use App\Assistants\Elastic\ValueObjects\Search\Result;
use App\Assistants\Elastic\ValueObjects\Search\Suggester;
use App\Assistants\Elastic\ValueObjects\Sorting;
use App\Convention\ValueObjects\Identity\Identity;
use App\Convention\ValueObjects\Meta\Meta;
use App\Convention\ValueObjects\Meta\Pagination;
use Arr;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Class ElasticService
 *
 * @package App\Assistants\Elastic\Services
 */
class ElasticService implements ElasticServiceContract
{
    /**
     * @var Client|null
     */
    private ?Client $client = null;

    /**
     * @var array
     */
    private array $lastResponse = [];

    /**
     * @var array
     */
    private array $successfulStatuses = [
        'created',
        'noop',
        'deleted',
    ];

    /**
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     *
     */
    private function init(): void
    {
        if (!$this->client instanceof Client) {
            $this->client = ClientBuilder::create()->setHosts(
                [
                    config('elasticsearch.host') . ":" . config('elasticsearch.port'),
                ]
            )->build();
        }
    }

    /**
     * @return Client
     */
    private function client(): Client
    {
        $this->init();

        return $this->client;
    }

    /**
     * @return array
     */
    private function lastResponse(): array
    {
        return $this->lastResponse;
    }

    /**
     * @param array $response
     */
    private function setLastResponse(array $response): void
    {
        $this->lastResponse = $response;
    }

    /**
     * @return bool
     */
    private function validateLastResponse(): bool
    {
        return in_array(Arr::get($this->lastResponse(), "result"), $this->successfulStatuses(), true) || Arr::get(
                $this->lastResponse(),
                "acknowledged",
                false
            );
    }

    /**
     * @return array
     */
    private function successfulStatuses(): array
    {
        return $this->successfulStatuses;
    }

    /**
     * @inheritDoc
     */
    public static function generateIndex(string $type): Index
    {
        return new Index($type);
    }

    /**
     * @inheritDoc
     */
    public function indexExists(Index $index): bool
    {
        return $this->client()->indices()->exists(['index' => $index->elasticIndex()]);
    }

    /**
     * @inheritDoc
     */
    public function recreateIndex(Index\AnalysisMapping $analysisMapping, Collection $mappings): bool
    {
        $params = collect(
            [
                'index' => $analysisMapping->index()->elasticIndex(),
                'body' => $analysisMapping->present(),
            ]
        );

        if ($this->client()->indices()->exists($params->only("index")->toArray())) {
            $this->client()->indices()->delete($params->only("index")->toArray());
        }

        $this->client()->indices()->create($params->toArray());

        $this->client()->indices()->close($params->only("index")->toArray());

        $this->updateFieldsMapping($analysisMapping->index(), $mappings);

        $this->client()->indices()->open($params->only("index")->toArray());

        sleep(3); // it is necessary to wait until index became available

        return true;
    }

    /**
     * @inheritDoc
     */
    public function updateAnalysisMapping(Index\AnalysisMapping $analysisMapping): bool
    {
        $params = collect(
            [
                'index' => $analysisMapping->index()->elasticIndex(),
                'body' => $analysisMapping->present(),
            ]
        );

        $response = $this->client()->indices()->putSettings($params->toArray());

        $this->setLastResponse($response);

        sleep(3); // it is necessary to wait until index became available

        return $this->validateLastResponse();
    }

    /**
     * @inheritDoc
     */
    public function fieldsMapping(Index $index): Collection
    {
        $existingMapping = $this->client()->indices()->getMapping(
            [
                "index" => $index->elasticIndex(),
                "type" => $index->type(),
            ]
        );

        return collect(
            Arr::get($existingMapping, "{$index->elasticIndex()}.mappings.{$index->type()}.properties", [])
        )->filter(
            static function (
                array $mapping,
                string $attribute
            ) {
                return Document::DOCUMENT_TYPE_KEY !== $attribute;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function updateFieldsMapping(Index $index, Collection $mappings): bool
    {
        $params = collect(
            [
                "index" => $index->elasticIndex(),
                "type" => $index->type(),
                "body" => [
                    "properties" => $mappings->toArray(),
                ],
            ]
        );

        if ($this->is7XVersion()) {
            $params->put('include_type_name', true);
        }

        $response = $this->client()->indices()->putMapping($params->toArray());

        $this->setLastResponse($response);

        sleep(3); // it is necessary to wait until index became available

        return $this->validateLastResponse();
    }

    /**
     * @inheritdoc
     */
    public function index(Index $index, IndexableContract $indexable, bool $refresh = true): bool
    {
        $response = $this->client()->update(
            array_merge(
                $indexable->asDocument($index)->present(),
                ['refresh' => $refresh]
            )
        );

        $this->setLastResponse($response);

        return $this->validateLastResponse();
    }

    /**
     * @inheritDoc
     */
    public function indexByQuery(
        Index $index,
        IndexableContract $indexable,
        Collection $needle,
        Body $data,
        bool $refresh = true
    ): bool {
        $params = collect(
            [
                'index' => $index->elasticIndex(),
                'type' => $index->type(),
                'refresh' => $refresh,
            ]
        );

        $body = collect($this->prepareFilter($index, $indexable, $needle));

        $body->put(
            'script',
            [
                'source' => $data->collection()->map(
                    static function ($value, string $key) {
                        return "ctx._source.{$key} = '$value'";
                    }
                )->implode(';'),
                'lang' => 'painless',
            ]
        );

        $params->put('body', $body->toArray());

        $response = $this->client()->updateByQuery($params->toArray());

        $this->setLastResponse($response);

        return $this->validateLastResponse();
    }

    /**
     * @inheritDoc
     */
    public function aggregations(
        Index $index,
        IndexableContract $indexable,
        Collection $needle = null,
        ?string $term = null
    ): Collection {
        $params = collect(
            [
                'index' => $index->elasticIndex(),
                'type' => $index->type(),
            ]
        );

        $aggregations = collect([]);

        $indexable->asMappings($index)->collection()->each(
            function (Mapping $mapping) use ($aggregations) {
                if ($mapping->type() === Mapping::TYPE_DATE) {
                    $aggregations->put(
                        "{$mapping->attribute()}_min",
                        [
                            "min" => [
                                "field" => $mapping->attribute(),
                            ],
                        ]
                    );
                    $aggregations->put(
                        "{$mapping->attribute()}_max",
                        [
                            "max" => [
                                "field" => $mapping->attribute(),
                            ],
                        ]
                    );
                } else {
                    $aggregations->put(
                        $mapping->attribute(),
                        [
                            "terms" => [
                                "field" => in_array(
                                    $mapping->type(),
                                    [
                                        Mapping::TYPE_STRING,
                                        Mapping::TYPE_NUMBER,
                                        Mapping::TYPE_ARRAY,
                                    ],
                                    true
                                ) ? "{$mapping->attribute()}.keyword" : $mapping->attribute(),
                                "size" => 10000,
                            ],
                        ]
                    );
                }
            }
        );

        $body = [
            'aggregations' => $aggregations,
        ];

        $body = array_merge($body, $this->prepareFilter($index, $indexable, $needle, $term));

        $params->put('body', $body);

        $response = $this->client()->search($params->toArray());

        return $this->parseAggregations($response);
    }

    /**
     * @param array $response
     *
     * @return Collection
     */
    private function parseAggregations(array $response): Collection
    {
        $collection = collect(Arr::get($response, "aggregations", []));

        return $collection->each(
            static function ($aggregation, $attribute) use ($collection) {
                if (strpos($attribute, '_min') || strpos($attribute, '_max')) {
                    $realAttribute = str_replace(['_min', '_max'], "", $attribute);
                    if ($collection->has($realAttribute)) {
                        $existing = $collection->get($realAttribute, []);
                        $existing = array_merge_recursive(
                            $existing,
                            [
                                'buckets' => [
                                    [
                                        'key' => Arr::get($aggregation, 'value_as_string', ''),
                                    ],
                                ],
                            ]
                        );
                        $collection->put($realAttribute, $existing);
                    } else {
                        $collection->put(
                            $realAttribute,
                            [
                                'buckets' => [
                                    [
                                        'key' => Arr::get($aggregation, 'value_as_string', ''),
                                    ],
                                ],
                            ]
                        );
                    }

                    $collection->forget($attribute);
                }
            }
        )->map(
            static function ($aggregation, $attribute) {
                $values = array_map(
                    static function (array $item) {
                        return Arr::get($item, "key_as_string", Arr::get($item, "key"));
                    },
                    Arr::get($aggregation, "buckets", [])
                );

                return new Aggregation(
                    $attribute, $values
                );
            }
        )->values();
    }

    /**
     * @inheritDoc
     */
    public function remove(Index $index, IndexableContract $indexable): bool
    {
        $params = [
            'id' => (string)$indexable->asIdentity(),
            'index' => $index->elasticIndex(),
            'type' => $index->type(),
            'refresh' => true,
        ];

        if (!$this->client()->exists($params)) {
            return true;
        }

        $response = $this->client()->delete($params);

        $this->setLastResponse($response);

        return $this->validateLastResponse();
    }

    /**
     * @inheritDoc
     */
    public function removeByQuery(Index $index, IndexableContract $indexable, Collection $needle): bool
    {
        $params = collect(
            [
                'index' => $index->elasticIndex(),
                'type' => $index->type(),
                'refresh' => true,
            ]
        );

        $body = collect($this->prepareFilter($index, $indexable, $needle));

        $params->put('body', $body->toArray());

        $response = $this->client()->deleteByQuery($params->toArray());

        $this->setLastResponse($response);

        return $this->validateLastResponse();
    }

    /**
     * @inheritDoc
     */
    public function filter(
        Index $index,
        SetupableContract $setupable,
        Collection $needle,
        Paginator $paginator,
        ?string $term = null,
        Collection $sortBy = null
    ): Collection {
        $params = collect(
            [
                'index' => $index->elasticIndex(),
                'type' => $index->type(),
                "size" => 10000,
            ]
        );

        if ($paginator->enabled()) {
            $params['from'] = $paginator->offset();
            $params['size'] = $paginator->limit();
        }

        $body = collect($this->prepareFilter($index, $setupable, $needle, $term));

        if ($sortBy instanceof Collection && $sortBy->isNotEmpty()) {
            $sort = $sortBy->map(
                static function (Sorting $sorting) {
                    return $sorting->toArray();
                }
            )->push(
                [
                    '_id' => Sorting::ASC,
                ]
            );

            $body->put('sort', $sort->toArray());
        }

        $params->put('body', $body->toArray());

        $response = $this->client()->search($params->toArray());

        if ($paginator->enabled()) {
            if ($this->is7XVersion()) {
                $key = 'hits.total.value';
            } else {
                $key = 'hits.total';
            }

            app()->make(Meta::class)->addPagination(new Pagination($paginator, Arr::get($response, $key, 0)));
        }

        return $this->parseSearch($response);
    }

    /**
     * @param Index             $index
     * @param SetupableContract $setupable
     * @param Collection|null   $needle
     * @param string|null       $term
     *
     * @return array
     */
    private function prepareFilter(
        Index $index,
        SetupableContract $setupable,
        ?Collection $needle = null,
        ?string $term = null
    ): array {
        if ($needle instanceof Collection) {
            $must = $needle->map(
                static function (NeedlePresenter $needle) {
                    return $needle->present();
                }
            );
        } else {
            $must = collect();
        }

        $must->push(
            [
                'match' => [
                    Document::DOCUMENT_TYPE_KEY . ".keyword" => $setupable->asType()->type(),
                ],
            ]
        );

        if (in_array($index->index(), Index::AVAILABLE_INDEXES, true)) {
            $searchIndex = $this::generateIndex(Index::INDEX_ENTITIES);
        } else {
            $searchIndex = $index;
        }

        if ($term !== null) {
            $searchResponse = $this->search($searchIndex, collect([$setupable]), $term);
            $must->push(
                [
                    'terms' => [
                        '_id' => $searchResponse->result()->map(
                            static function (array $params) {
                                return (string)Arr::get($params, '_id');
                            }
                        )->values()->toArray(),
                    ],
                ]
            );
        }

        return [
            'query' => [
                'bool' => [
                    'must' => $must->values()->toArray(),
                ],
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function search(
        Index $index,
        Collection $indexables,
        string $term,
        Suggester $suggester = null,
        int $size = 10000
    ): Result {
        $params = collect(
            [
                'index' => $index->elasticIndex(),
                'type' => $index->type(),
                'size' => $size,
            ]
        );

        $params->put('body', $this->prepareSearch($indexables, $term));

        if ($suggester instanceof Suggester) {
            $body = $params->get('body', []);
            Arr::set($body, 'suggest', $suggester->present());

            $params->put('body', $body);
        }

        $response = $this->client()->search($params->toArray());

        $this->parseSearch($response);

        return new Result(
            $this->parseSearch($response),
            $suggester instanceof Suggester ? $this->parseSuggest($response, $suggester) : collect()
        );
    }

    /**
     * @inheritdoc
     */
    public function autocomplete(
        Index $index,
        Collection $indexables,
        string $term,
        Collection $needle,
        int $size = 10000
    ): Collection {
        $params = collect(
            [
                'index' => $index->elasticIndex(),
                'type' => $index->type(),
                'size' => $size,
            ]
        );

        $params->put('body', $this->prepareSearch($indexables, $term, $needle));

        $body = $params->get('body', []);

        Arr::set(
            $body,
            'aggs',
            [
                'autocomplete' => [
                    'terms' => [
                        'field' => 'autocomplete',
                        'include' => "$term.*",
                        'order' => [
                            '_count' => 'desc',
                        ],
                    ],
                    'aggs' => [
                        'top_hits' => [
                            'top_hits' => [
                                '_source' => [
                                    'includes' => ['_id', '_document_type'],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $params->put('body', $body);

        $response = $this->client()->search($params->toArray());

        return $this->parseAutocomplete($response);
    }

    /**
     * @param Collection $setupables
     * @param string     $term
     * @param Collection $needle
     *
     * @return array
     */
    private function prepareSearch(Collection $setupables, string $term, ?Collection $needle = null): array
    {
        $must = collect();

        if ($setupables->count() === 1) {
            $must->push(
                [
                    'match' => [
                        Document::DOCUMENT_TYPE_KEY . ".keyword" => $setupables->first()->asType()->type(),
                    ],
                ]
            );
        }

        $must->push($this->_searchQueryString($term));

        if ($needle instanceof Collection) {
            $needle->each(
                static function (Needle $needle) use (&$must) {
                    $must->push($needle->present());
                }
            );
        }

        return [
            'query' => [
                'bool' => [
                    'must' => $must->values()->toArray(),
                ],
            ],
        ];
    }

    /**
     * @param string $term
     *
     * @return array
     */
    private function _searchQueryString(string $term): array
    {
        return [
            "query_string" => [
                "default_operator" => "AND",
                "query" => "*{$term}*",
            ],
        ];
    }

    /**
     * @param array $response
     *
     * @return Collection
     */
    private function parseSearch(array $response): Collection
    {
        return collect(Arr::get($response, "hits.hits", []))->map(
            static function ($hit) {
                $result = Arr::get($hit, '_source', []);
                $result['_id'] = new Identity(Arr::get($hit, '_id'));

                return $result;
            }
        )->values();
    }

    /**
     * @param array     $response
     * @param Suggester $suggester
     *
     * @return Collection
     */
    private function parseSuggest(array $response, Suggester $suggester): Collection
    {
        return collect(Arr::get($response, "suggest.{$suggester->analyzer()}.0.options", []))->sortByDesc(
            static function (
                $hit
            ) {
                return Arr::get($hit, 'score', []);
            }
        )->map(
            static function ($hit) {
                return Arr::get($hit, 'text', []);
            }
        )->values();
    }

    /**
     * @param array $response
     *
     * @return Collection
     */
    private function parseAutocomplete(array $response): Collection
    {
        $collected = collect();
        collect(Arr::get($response, "aggregations", []))->each(
            function ($aggregation) use (&$collected) {
                collect(Arr::get($aggregation, "buckets", []))->each(
                    function (array $bucket) use (&$collected) {
                        $collected->put(
                            Arr::get($bucket, 'key', ''),
                            $this->parseSearch(Arr::get($bucket, 'top_hits', []))->values()
                        );
                    }
                );
            }
        );

        return $collected->filter();
    }

    /**
     * @inheritDoc
     */
    public function terms(Index $index, IndexableContract $indexable, Collection $terms): Collection
    {
        $params = collect(
            [
                'index' => $index->elasticIndex(),
                'type' => $index->type(),
                "size" => 10000,
            ]
        );

        $indexable->asMappings($index);

        $params->put(
            'body',
            [
                'query' => [
                    'terms' => [
                        '_id' => $terms->values()->toArray(),
                    ],
                ],
            ]
        );
        $response = $this->client()->search($params->toArray());

        return $this->parseSearch($response);
    }

    /**
     * todo remove it after test env will use the 7.x elasticversion
     *
     * @return bool
     */
    private function is7XVersion(): bool
    {
        return Str::startsWith(Arr::get($this->client()->info(), 'version.number'), '7');
    }
}
