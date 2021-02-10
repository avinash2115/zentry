<?php

namespace App\Components\Sessions\Session\Mutators\DTO\Traits;

use App\Components\Sessions\Session\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Sessions\Session\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $sessionMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function sessionMutator__(): Mutator
    {
        if (!$this->sessionMutator instanceof Mutator) {
            $this->sessionMutator = app()->make(Mutator::class);
        }

        return $this->sessionMutator;
    }
}
