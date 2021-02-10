<?php

namespace App\Components\Sessions\Session\Progress\Mutators\DTO\Traits;

use App\Components\Sessions\Session\Progress\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Sessions\Session\Progress\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $sessionProgressMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function sessionProgressMutator__(): Mutator
    {
        if (!$this->sessionProgressMutator instanceof Mutator) {
            $this->sessionProgressMutator = app()->make(Mutator::class);
        }

        return $this->sessionProgressMutator;
    }
}
