<?php

namespace App\Assistants\Elastic\ValueObjects\Index;

use App\Assistants\Elastic\ValueObjects\Index;
use Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class AnalysisMapping
 *
 * @package App\Assistants\Elastic\ValueObjects\Index
 */
final class AnalysisMapping
{
    public const ANALYZER = 'analyzer';

    private const NORMALIZER = 'normalizer';

    public const FILTER = 'filter';

    /**
     * @var Index
     */
    private Index $index;

    /**
     * @var Collection
     */
    private Collection $filters;

    /**
     * @var Collection
     */
    private Collection $analyzers;

    /**
     * @var Collection
     */
    private Collection $normalizers;

    /**
     * @param Index      $index
     * @param Collection $filters
     * @param Collection $analyzers
     * @param Collection $normalizers
     */
    public function __construct(Index $index, Collection $filters, Collection $analyzers, Collection $normalizers)
    {
        $this->setIndex($index);
        $this->setFilters($filters);
        $this->setAnalyzers($analyzers);
        $this->setNormalizers($normalizers);
    }

    /**
     * @return Index
     */
    public function index(): Index
    {
        return $this->index;
    }

    /**
     * @param Index $index
     */
    private function setIndex(Index $index): void
    {
        $this->index = $index;
    }

    /**
     * @param Collection $filters
     *
     * @return AnalysisMapping
     */
    private function setFilters(Collection $filters): AnalysisMapping
    {
        $filters->each(
            static function (array $filter) {
                if (!Arr::has($filter, "type")) {
                    throw new InvalidArgumentException("Filter must contain type");
                }
            }
        );

        $this->filters = $filters;

        return $this;
    }

    /**
     * @return Collection
     */
    public function filters(): Collection
    {
        return $this->filters;
    }

    /**
     * @param Collection $analyzers
     *
     * @return AnalysisMapping
     */
    private function setAnalyzers(Collection $analyzers): AnalysisMapping
    {
        $analyzers->each(
            static function (array $filter) {
                if (!Arr::has($filter, "type")) {
                    throw new InvalidArgumentException("Analyzer must contain type");
                }
                if (!Arr::has($filter, "filter") || !is_array(Arr::get($filter, "filter"))) {
                    throw new InvalidArgumentException("Analyzer must contain filter as an array");
                }
            }
        );

        $this->analyzers = $analyzers;

        return $this;
    }

    /**
     * @param Collection $normalizers
     *
     * @return AnalysisMapping
     */
    private function setNormalizers(Collection $normalizers): AnalysisMapping
    {
        $normalizers->each(
            static function (array $filter) {
                if (!Arr::has($filter, "type")) {
                    throw new InvalidArgumentException("Analyzer must contain type");
                }
                if (!Arr::has($filter, "filter") || !is_array(Arr::get($filter, "filter"))) {
                    throw new InvalidArgumentException("Analyzer must contain filter as an array");
                }
            }
        );

        $this->normalizers = $normalizers;

        return $this;
    }

    /**
     * @return Collection
     */
    public function analyzers(): Collection
    {
        return $this->analyzers;
    }

    /**
     * @return Collection
     */
    public function normalizers(): Collection
    {
        return $this->normalizers;
    }

    /**
     * @return array
     */
    public function present(): array
    {
        $analysis = [];

        if ($this->filters()->isNotEmpty()) {
            $analysis[self::FILTER] = $this->filters()->toArray();
        }

        if ($this->analyzers()->isNotEmpty()) {
            $analysis[self::ANALYZER] = $this->analyzers()->toArray();
        }

        if ($this->normalizers()->isNotEmpty()) {
            $analysis[self::NORMALIZER] = $this->normalizers()->toArray();
        }

        return [
            "settings" => [
                "index" => [
                    "analysis" => $analysis,
                ],
            ],
        ];
    }
}
