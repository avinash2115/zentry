<?php

namespace App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Traits;

use App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Users\Participant\Goal\Tracker\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $trackerMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function trackerMutator__(): Mutator
    {
        if (!$this->trackerMutator instanceof Mutator) {
            $this->trackerMutator = app()->make(Mutator::class);
        }

        return $this->trackerMutator;
    }
}
