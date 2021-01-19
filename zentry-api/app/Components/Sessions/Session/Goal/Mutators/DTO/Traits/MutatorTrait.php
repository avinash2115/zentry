<?php

namespace App\Components\Sessions\Session\Goal\Mutators\DTO\Traits;

use App\Components\Sessions\Session\Goal\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Sessions\Session\Goal\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $sessionGoalMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function sessionGoalMutator__(): Mutator
    {
        if (!$this->sessionGoalMutator instanceof Mutator) {
            $this->sessionGoalMutator = app()->make(Mutator::class);
        }

        return $this->sessionGoalMutator;
    }
}
