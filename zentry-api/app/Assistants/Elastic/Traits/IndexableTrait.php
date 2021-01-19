<?php

namespace App\Assistants\Elastic\Traits;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Elastic\Events\StateCreateOrUpdate;
use App\Assistants\Elastic\Events\StateDeletion;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Queueable;
use App\Assistants\Events\EventRegistry;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait IndexableTrait
 *
 * @see     IndexableContract
 * @package App\Assistants\Elastic\Traits
 */
trait IndexableTrait
{
    /**
     * @inheritdoc
     */
    public function forQueue(Index $index): Queueable
    {
        return new Queueable(
            $this->asIdentity(), $this->asType(), $this->asDocument($index), $this->asMappings($index)
        );
    }

    /**
     * @inheritdoc
     */
    public function stateChanged(bool $withJob = true): void
    {
        $this->pushEvent(StateCreateOrUpdate::class, $withJob);
    }

    /**
     * @inheritdoc
     */
    public function stateDeletion(bool $withJob = true): void
    {
        $this->pushEvent(StateDeletion::class, $withJob);
    }

    /**
     * @param string $class
     * @param bool   $withJob
     *
     * @throws BindingResolutionException
     */
    protected function pushEvent(string $class, bool $withJob): void
    {
        app()->make(EventRegistry::class)->register(new $class(clone $this, $withJob));
    }
}
