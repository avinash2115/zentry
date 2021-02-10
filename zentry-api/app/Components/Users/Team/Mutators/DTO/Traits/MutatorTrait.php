<?php

namespace App\Components\Users\Team\Mutators\DTO\Traits;

use App\Components\Users\Team\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Users\Team\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $teamMutator__ = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function teamMutator__(): Mutator
    {
        if (!$this->teamMutator__ instanceof Mutator) {
            $this->teamMutator__ = app()->make(Mutator::class);
        }

        return $this->teamMutator__;
    }
}
