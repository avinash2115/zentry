<?php

namespace App\Assistants\Elastic\Jobs;

use App\Assistants\Elastic\Traits\ElasticServiceTrait;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Queueable as QueueableVO;
use App\Convention\Jobs\Base\Job;

/**
 * Class AddOrUpdateEntityToIndex
 *
 * @package App\Assistants\Elastic\Jobs
 */
class AddOrUpdateEntityToIndex extends Job
{
    use ElasticServiceTrait;

    /**
     * @var QueueableVO
     */
    private QueueableVO $queueable;

    /**
     * @var Index
     */
    private Index $index;

    /**
     * @param QueueableVO            $queueable
     * @param Index                  $index
     */
    public function __construct(QueueableVO $queueable, Index $index)
    {
        $this->queueable = $queueable;
        $this->index = $index;
    }

    /**
     * @inheritDoc
     */
    protected function _handle(): void
    {
        $this->elasticService__()->index($this->index, $this->queueable);
    }
}
