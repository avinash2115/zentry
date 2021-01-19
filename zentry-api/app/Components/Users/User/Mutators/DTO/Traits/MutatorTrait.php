<?php

namespace App\Components\Users\User\Mutators\DTO\Traits;

use App\Components\Users\User\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Users\User\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $userMutator__ = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function userMutator__(): Mutator
    {
        if (!$this->userMutator__ instanceof Mutator) {
            $this->userMutator__ = app()->make(Mutator::class);
            $this->userMutator__->simplifiedMutation();
        }

        return $this->userMutator__;
    }
}
