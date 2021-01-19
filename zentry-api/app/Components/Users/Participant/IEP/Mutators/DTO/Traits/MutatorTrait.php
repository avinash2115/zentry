<?php

namespace App\Components\Users\Participant\IEP\Mutators\DTO\Traits;

use App\Components\Users\Participant\IEP\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Users\Participant\IEP\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $iepMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function iepMutator__(): Mutator
    {
        if (!$this->iepMutator instanceof Mutator) {
            $this->iepMutator = app()->make(Mutator::class);
        }

        return $this->iepMutator;
    }
}
