<?php

namespace App\Assistants\Elastic\Services;

use App\Assistants\Elastic\Contracts\Indexable\SetupableContract;
use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Elastic\ValueObjects\Body;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Paginator;
use App\Assistants\Elastic\ValueObjects\Search\Result;
use App\Assistants\Elastic\ValueObjects\Search\Suggester;
use Illuminate\Support\Collection;

/**
 * Class ElasticMemoryService
 *
 * @package App\Assistants\Elastic\Services
 */
class ElasticMemoryService implements ElasticServiceContract
{
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
        return true;
    }

    /**
     * @inheritDoc
     */
    public function recreateIndex(Index\AnalysisMapping $analysisMapping, Collection $mappings): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function updateAnalysisMapping(Index\AnalysisMapping $analysisMapping): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function fieldsMapping(Index $index): Collection
    {
        return collect();
    }

    /**
     * @inheritDoc
     */
    public function updateFieldsMapping(Index $index, Collection $mappings): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function index(Index $index, IndexableContract $indexable, bool $refresh = true): bool
    {
        return true;
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
        return true;
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
        return collect();
    }

    /**
     * @inheritDoc
     */
    public function remove(Index $index, IndexableContract $indexable): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function removeByQuery(Index $index, IndexableContract $indexable, Collection $needle): bool
    {
        return true;
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
        ?Collection $sortBy = null
    ): Collection {
        return collect();
    }

    /**
     * @inheritDoc
     */
    public function search(
        Index $index,
        Collection $indexables,
        string $term,
        Suggester $suggester = null,
        int $size = 1000
    ): Result {
        return new Result(collect(), collect());
    }

    /**
     * @inheritDoc
     */
    public function autocomplete(
        Index $index,
        Collection $indexables,
        string $term,
        Collection $needle,
        int $size = 1000
    ): Collection {
        return collect();
    }

    /**
     * @inheritDoc
     */
    public function terms(Index $index, IndexableContract $indexable, Collection $terms): Collection
    {
        return collect();
    }
}
