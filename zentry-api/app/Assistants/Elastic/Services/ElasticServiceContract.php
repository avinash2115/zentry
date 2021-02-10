<?php

namespace App\Assistants\Elastic\Services;

use App\Assistants\Elastic\Contracts\Indexable\SetupableContract;
use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\ValueObjects\Body;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Paginator;
use App\Assistants\Elastic\ValueObjects\Search\Result;
use App\Assistants\Elastic\ValueObjects\Search\Suggester;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface ElasticServiceContract
 *
 * @package App\Assistants\Elastic\Services
 */
interface ElasticServiceContract
{
    public const KEYWORD_IGNORE_ABOVE_LENGTH = 1024;

    /**
     * @param string $type
     *
     * @return Index
     * @throws InvalidArgumentException
     */
    public static function generateIndex(string $type): Index;

    /**
     * @param Index $index
     *
     * @return bool
     */
    public function indexExists(Index $index): bool;

    /**
     * @param Index\AnalysisMapping $analysisMapping
     * @param Collection            $mappings
     *
     * @return bool
     */
    public function recreateIndex(Index\AnalysisMapping $analysisMapping, Collection $mappings): bool;

    /**
     * @param Index\AnalysisMapping $analysisMapping
     *
     * @return bool
     */
    public function updateAnalysisMapping(Index\AnalysisMapping $analysisMapping): bool;

    /**
     * @param Index $index
     *
     * @return Collection
     */
    public function fieldsMapping(Index $index): Collection;

    /**
     * @param Index      $index
     * @param Collection $mappings
     *
     * @return bool
     */
    public function updateFieldsMapping(Index $index, Collection $mappings): bool;

    /**
     * @param Index             $index
     * @param IndexableContract $indexable
     * @param bool              $refresh
     *
     * @return bool
     * @throws IndexNotSupported
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    public function index(Index $index, IndexableContract $indexable, bool $refresh = true): bool;

    /**
     * @param Index             $index
     * @param IndexableContract $indexable
     * @param Collection        $needle
     * @param Body              $data
     * @param bool              $refresh
     *
     * @return bool
     * @throws IndexNotSupported
     */
    public function indexByQuery(
        Index $index,
        IndexableContract $indexable,
        Collection $needle,
        Body $data,
        bool $refresh = true
    ): bool;

    /**
     * @param Index             $index
     * @param IndexableContract $indexable
     * @param Collection|null   $needle
     * @param null|string       $term
     *
     * @return Collection
     * @throws IndexNotSupported
     */
    public function aggregations(
        Index $index,
        IndexableContract $indexable,
        Collection $needle = null,
        ?string $term = null
    ): Collection;

    /**
     * @param Index             $index
     * @param IndexableContract $indexable
     *
     * @return bool
     * @throws IndexNotSupported|PropertyNotInit
     */
    public function remove(Index $index, IndexableContract $indexable): bool;

    /**
     * @param Index             $index
     * @param IndexableContract $indexable
     * @param Collection        $needle
     *
     * @return bool
     */
    public function removeByQuery(
        Index $index,
        IndexableContract $indexable,
        Collection $needle
    ): bool;

    /**
     * @param Index             $index
     * @param SetupableContract $setupable
     * @param Collection        $needle
     * @param Paginator         $paginator
     * @param string|null       $term
     * @param Collection|null   $sortBy
     *
     * @return Collection
     * @throws BindingResolutionException
     */
    public function filter(
        Index $index,
        SetupableContract $setupable,
        Collection $needle,
        Paginator $paginator,
        ?string $term = null,
        ?Collection $sortBy = null
    ): Collection;

    /**
     * @param Index          $index
     * @param Collection     $indexables
     * @param string         $term
     * @param Suggester|null $suggester
     * @param int            $size
     *
     * @return Result
     */
    public function search(
        Index $index,
        Collection $indexables,
        string $term,
        Suggester $suggester = null,
        int $size = 1000
    ): Result;

    /**
     * @param Index      $index
     * @param Collection $indexables
     * @param string     $term
     * @param Collection $needle
     * @param int        $size
     *
     * @return Collection
     */
    public function autocomplete(
        Index $index,
        Collection $indexables,
        string $term,
        Collection $needle,
        int $size = 1000
    ): Collection;

    /**
     * @param Index             $index
     * @param IndexableContract $indexable
     * @param Collection        $terms
     *
     * @return Collection
     * @throws IndexNotSupported
     * @throws RuntimeException
     * @throws BindingResolutionException
     */
    public function terms(Index $index, IndexableContract $indexable, Collection $terms): Collection;
}
