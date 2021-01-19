<?php

namespace App\Components\Users\Participant\Goal\Mutators\DTO\Traits;

use App\Components\Users\Participant\Goal\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Users\Participant\Goal\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $goalMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function goalMutator__(): Mutator
    {
        if (!$this->goalMutator instanceof Mutator) {
            $this->goalMutator = app()->make(Mutator::class);
        }

        return $this->goalMutator;
    }
}
