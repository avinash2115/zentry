<?php

namespace App\Assistants\Search\Services;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Elastic\Contracts\Indexable\SetupableContract;
use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Services\ElasticServiceContract;
use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Mapping;
use App\Assistants\Elastic\ValueObjects\Search\Suggester;
use App\Assistants\Search\Agency\Services\Searchable;
use App\Assistants\Search\Search\Autocomplete\AutocompletedDTO;
use App\Assistants\Search\Search\SearchDTO;
use App\Components\Sessions\Services\Poi\Indexable\SetupService;
use App\Components\Sessions\Services\SessionService;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Psy\Exception\FatalErrorException;
use UnexpectedValueException;

/**
 * Class SearchService
 *
 * @package App\Assistants\Search\Services
 */
class SearchService implements SearchServiceContract
{
    use ElasticServiceTrait;

    public const AVAILABLE_SUBJECTS = [
        SessionServiceContract::class,
        SetupService::class
    ];

    public const RESULTS_LIMIT_FOR_GLOBAL_SEARCH = 10;

    /**
     * @var IndexableContract | null
     */
    private ?IndexableContract $indexable = null;

    /**
     * @return IndexableContract|null
     */
    private function _indexable(): ?IndexableContract
    {
        return $this->indexable;
    }

    /**
     * @param IndexableContract $indexable
     *
     * @return SearchService
     */
    private function setIndexable(IndexableContract $indexable): SearchService
    {
        $this->indexable = $indexable;

        return $this;
    }

    /**
     * @return SearchService
     */
    private function clearIndexable(): SearchService
    {
        $this->indexable = null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setup(): bool
    {
        $index = $this->elasticService__()::generateIndex(Index::INDEX_ENTITIES);

        $filters = collect(
            [
                'stemmer' => [
                    'type' => 'stemmer',
                    'language' => 'english',
                ],
                'autocompleteFilter' => [
                    'max_shingle_size' => '4',
                    'min_shingle_size' => '2',
                    'type' => 'shingle',
                ],
                'stopwords' => [
                    'type' => 'stop',
                    'stopwords' => ['_english_'],
                ],
            ]
        );

        $analyzers = collect(
            [
                'didYouMean' => [
                    'filter' => ['lowercase'],
                    'char_filter' => ['html_strip'],
                    'type' => 'custom',
                    'tokenizer' => 'standard',
                ],
                'autocomplete' => [
                    'filter' => ['lowercase', 'autocompleteFilter'],
                    'char_filter' => ['html_strip'],
                    'type' => 'custom',
                    'tokenizer' => 'whitespace',
                ],
                'default' => [
                    'filter' => ['lowercase', 'stopwords', 'stemmer'],
                    'char_filter' => ['html_strip'],
                    'type' => 'custom',
                    'tokenizer' => 'standard',
                ],
                'lowercase_hypen' => [
                    'type' => 'custom',
                    'tokenizer' => 'keyword',
                    'filter' => ['lowercase'],
                    'char_filter' => ['html_strip'],
                ],
            ]
        );

        $analysisMapping = new Index\AnalysisMapping($index, $filters, $analyzers, collect());

        $mappings = collect();

        $this->_subjects()->each(
            static function (SetupableContract $setupable) use ($index, $mappings) {
                try {
                    $setupable->asMappings($index)->collection()->filter(
                        static function (Mapping $mapping) {
                            return $mapping->isType(Mapping::TYPE_STRING) || $mapping->isType(Mapping::TYPE_LONG_TEXT);
                        }
                    )->each(
                        static function (Mapping $mapping) use ($mappings) {
                            if ($mapping->isType(Mapping::TYPE_STRING)) {
                                $mappings->put(
                                    $mapping->attribute(),
                                    [
                                        'type' => 'text',
                                        'fields' => [
                                            'keyword' => [
                                                'type' => 'keyword',
                                                'ignore_above' => ElasticServiceContract::KEYWORD_IGNORE_ABOVE_LENGTH,
                                            ],
                                        ],
                                        'analyzer' => 'lowercase_hypen',
                                        'copy_to' => ['autocomplete', 'did_you_mean'],
                                    ]
                                );
                            } else {
                                $mappings->put(
                                    $mapping->attribute(),
                                    [
                                        'type' => 'text',
                                        'fields' => [
                                            'autocompleted' => [
                                                'type' => 'text',
                                                'analyzer' => 'autocomplete',
                                            ],
                                        ]
                                    ]
                                );
                            }
                        }
                    );
                } catch (IndexNotSupported $exception) {
                }
            }
        );

        $mappings->put(
            'autocomplete',
            [
                'type' => 'text',
                'fielddata' => true,
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => ElasticServiceContract::KEYWORD_IGNORE_ABOVE_LENGTH,
                    ],
                ],
                'analyzer' => 'autocomplete',
            ]
        );

        $mappings->put(
            'did_you_mean',
            [
                'type' => 'text',
                'analyzer' => 'didYouMean',
            ]
        );

        return $this->elasticService__()->recreateIndex($analysisMapping, $mappings);
    }

    /**
     * @inheritDoc
     */
    public function directly(IndexableContract $indexable): SearchServiceContract
    {
        $this->setIndexable($indexable);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function globally(): SearchServiceContract
    {
        $this->clearIndexable();

        return $this;
    }

    /**
     * @return Collection
     */
    private function _subjects(): Collection
    {
        if ($this->_indexable() instanceof IndexableContract) {
            return collect([$this->_indexable()]);
        }

        return collect(self::AVAILABLE_SUBJECTS)->map(
            static function (string $abstract) {
                return app()->make($abstract);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function search(string $term): SearchDTO
    {
        $term = self::sanitize($term);

        $subjects = $this->_subjects();

        $result = $this->elasticService__()->search(
            $this->elasticService__()::generateIndex(Index::INDEX_ENTITIES),
            $subjects,
            $term,
            new Suggester('didYouMean', 'did_you_mean', $term)
        );

        $resultByTypes = $result->result()->groupBy(
            static function (array $hit) {
                return Arr::get($hit, '_document_type', 'no_type');
            }
        );

        $searchDTO = $this->createSearchDTO();

        $subjects->each(
            function (SetupableContract $indexable) use (&$searchDTO, $resultByTypes) {
                $result = $resultByTypes->get($indexable->asType()->type());
                if ($searchDTO->relationships instanceof Collection && $result instanceof Collection && $result->isNotEmpty(
                    )) {
                    $searchDTO->relationships->put(
                        $indexable->asType()->type(),
                        $this->verifyResult(
                            $indexable,
                            $result,
                            collect(['limit' => self::RESULTS_LIMIT_FOR_GLOBAL_SEARCH])
                        )
                    );
                }
            }
        );

        $searchDTO->fillMeta(
            [
                'search' => [
                    'suggest' => $result->suggested()->first(),
                ],
            ]
        );

        return $searchDTO;
    }

    /**
     * @inheritDoc
     */
    public function autocomplete(string $term, ?Collection $needle = null): Collection
    {
        $term = self::sanitize($term);
        $subjects = $this->_subjects();

        $results = $this->elasticService__()->autocomplete(
            $this->elasticService__()::generateIndex(Index::INDEX_ENTITIES),
            $subjects,
            $term,
            $needle ?? collect()
        );

        return $results->filter(
            function (Collection $documents) {
                $documents = $documents->groupBy(
                    static function (array $hit) {
                        return Arr::get($hit, '_document_type', 'no_type');
                    }
                );

                if ($this->_indexable() instanceof IndexableContract) {
                    return $this->verifyAutoComplete(
                        $this->_indexable(),
                        $documents->get(
                            $this->_indexable()->asType()->type()
                        )
                    );
                }

                $indexable = $this->_subjects()->first(
                    function (IndexableContract $indexable) use ($documents) {
                        return $this->verifyAutoComplete($indexable, $documents->get($indexable->asType()->type()));
                    }
                );

                return $indexable instanceof IndexableContract;
            }
        )->map(
            function (Collection $documents, string $autocompleteText) {
                return $this->createAutocompleteDTO($autocompleteText);
            }
        )->values();
    }

    /**
     * @param SetupableContract $indexable
     * @param Collection|null   $result
     * @param Collection|null   $filters
     *
     * @return Collection
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     */
    private function verifyResult(
        SetupableContract $indexable,
        ?Collection $result = null,
        ?Collection $filters = null
    ): Collection {
        if ($indexable instanceof Searchable) {
            if ($result instanceof Collection) {
                return $indexable->verifyResults(
                    $result->map(
                        static function (array $value) {
                            return (string)Arr::get($value, '_id');
                        }
                    ),
                    $filters
                );
            }

            return $indexable->verifyResults(collect(), $filters);
        }

        throw new UnexpectedValueException(
            "Indexable class with type {$indexable->asType()->type()} must implement Searchable interface."
        );
    }

    /**
     * @param IndexableContract $indexable
     * @param Collection|null   $result
     *
     * @return bool
     * @throws UnexpectedValueException
     */
    private function verifyAutoComplete(
        IndexableContract $indexable,
        ?Collection $result = null
    ): bool {
        if ($indexable instanceof Searchable) {
            if ($result instanceof Collection) {
                return $indexable->verifyAutocomplete(
                    $result->map(
                        static function (array $value) {
                            return (string)Arr::get($value, '_id');
                        }
                    )
                );
            }

            return false;
        }

        throw new UnexpectedValueException(
            "Indexable class with type {$indexable->asType()->type()} must implement Searchable interface."
        );
    }

    /**
     * @return SearchDTO
     */
    private function createSearchDTO(): SearchDTO
    {
        $searchDTO = new SearchDTO();
        $searchDTO->id = IdentityGenerator::next()->toString();
        $searchDTO->relationships = collect();

        return $searchDTO;
    }

    /**
     * @param string $value
     *
     * @return AutocompletedDTO
     */
    private function createAutocompleteDTO(string $value): AutocompletedDTO
    {
        $autocompleteDTO = new AutocompletedDTO();
        $autocompleteDTO->id = IdentityGenerator::next()->toString();
        $autocompleteDTO->value = $value;

        return $autocompleteDTO;
    }

    /**
     * @param string $term
     *
     * @return string
     */
    public static function sanitize(string $term): string
    {
        return strtolower(
            (string)preg_replace('/([&&||\!\(\)\{\}\[\]\^\"\~\*\?\:\/]|\bAND\b|\bOR\b)/', '\\\$1', $term)
        );
    }
}
