<?php

namespace App\Components\Users\Team\School\Mutators\DTO\Traits;

use App\Components\Users\Team\School\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Users\Team\School\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $schoolMutator__ = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function schoolMutator__(): Mutator
    {
        if (!$this->schoolMutator__ instanceof Mutator) {
            $this->schoolMutator__ = app()->make(Mutator::class);
        }

        return $this->schoolMutator__;
    }
}
