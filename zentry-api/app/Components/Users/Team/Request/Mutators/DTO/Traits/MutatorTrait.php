<?php

namespace App\Components\Users\Team\Request\Mutators\DTO\Traits;

use App\Components\Users\Team\Request\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Users\Team\Request\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $teamRequestMutator__ = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function teamRequestMutator__(): Mutator
    {
        if (!$this->teamRequestMutator__ instanceof Mutator) {
            $this->teamRequestMutator__ = app()->make(Mutator::class);
        }

        return $this->teamRequestMutator__;
    }
}
