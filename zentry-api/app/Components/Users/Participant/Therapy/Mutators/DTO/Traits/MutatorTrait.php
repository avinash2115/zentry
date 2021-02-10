<?php

namespace App\Components\Users\Participant\Therapy\Mutators\DTO\Traits;

use App\Components\Users\Participant\Therapy\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Users\Participant\Therapy\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $therapyMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function therapyMutator__(): Mutator
    {
        if (!$this->therapyMutator instanceof Mutator) {
            $this->therapyMutator = app()->make(Mutator::class);
        }

        return $this->therapyMutator;
    }
}
