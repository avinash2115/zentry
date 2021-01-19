<?php

namespace App\Assistants\Elastic\ValueObjects\Search;

use Illuminate\Support\Collection;

/**
 * Class Result
 *
 * @package App\Assistants\Elastic\ValueObjects\Search
 */
final class Result
{
    /**
     * @var Collection
     */
    private Collection $result;

    /**
     * @var Collection
     */
    private Collection $suggested;

    /**
     * @param Collection $result
     * @param Collection $suggested
     */
    public function __construct(Collection $result, Collection $suggested)
    {
        $this->setResult($result);
        $this->setSuggested($suggested);
    }

    /**
     * @return Collection
     */
    public function result(): Collection
    {
        return $this->result;
    }

    /**
     * @param Collection $result
     *
     * @return Result
     */
    private function setResult(Collection $result): Result
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return Collection
     */
    public function suggested(): Collection
    {
        return $this->suggested;
    }

    /**
     * @param Collection $suggested
     *
     * @return Result
     */
    private function setSuggested(Collection $suggested): Result
    {
        $this->suggested = $suggested;

        return $this;
    }
}
