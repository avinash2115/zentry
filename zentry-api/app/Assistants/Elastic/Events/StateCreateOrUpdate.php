<?php

namespace App\Assistants\Elastic\Events;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use Illuminate\Queue\SerializesModels;

/**
 * Class StateCreateOrUpdate
 *
 * @package App\Assistants\Elastic\Events
 */
class StateCreateOrUpdate
{
    use SerializesModels;

    /**
     * @var IndexableContract
     */
    private IndexableContract $indexable;

    /**
     * @var bool
     */
    private bool $withJob;

    /**
     * StateCreateOrUpdate constructor.
     *
     * @param IndexableContract $indexable
     * @param bool              $withJob
     */
    public function __construct(IndexableContract $indexable, bool $withJob)
    {
        $this->indexable = $indexable;
        $this->withJob = $withJob;
    }

    /**
     * @return IndexableContract
     */
    public function indexable(): IndexableContract
    {
        return $this->indexable;
    }

    /**
     * @return bool
     */
    public function withJob(): bool
    {
        return $this->withJob;
    }
}
