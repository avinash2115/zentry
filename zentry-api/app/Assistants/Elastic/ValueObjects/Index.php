<?php

namespace App\Assistants\Elastic\ValueObjects;

use InvalidArgumentException;

/**
 * Class Index
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class Index
{
    public const INDEX_FILTERS = 'filters';
    public const INDEX_LABELS = 'labels';
    public const INDEX_ENTITIES = 'entities';
    public const AVAILABLE_INDEXES = [
        self::INDEX_FILTERS,
        self::INDEX_LABELS,
        self::INDEX_ENTITIES,
    ];
    public const INDEX_TYPE = 'doc';

    /**
     * @var string
     */
    private string $index;

    /**
     * @var string
     */
    private string $elasticIndex;

    /**
     * @param string $index
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $index)
    {
        $this->setIndex($index);
    }

    /**
     * @return string
     */
    public function index(): string
    {
        return $this->index;
    }

    /**
     * @param string $index
     *
     * @return Index
     * @throws InvalidArgumentException
     */
    private function setIndex(string $index): self
    {
        if (!in_array($index, self::AVAILABLE_INDEXES, true)) {
            throw new InvalidArgumentException("Index {$index} is not allowed");
        }

        $this->index = $index;

        $this->setElasticIndex($index);

        return $this;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return self::INDEX_TYPE;
    }

    /**
     * @return string
     */
    public function elasticIndex(): string
    {
        return $this->elasticIndex;
    }

    /**
     * @param string $index
     *
     * @return Index
     */
    private function setElasticIndex(string $index): self
    {
        $env = env('APP_ENV');

        $this->elasticIndex = "{$env}_$index";

        return $this;
    }
}
