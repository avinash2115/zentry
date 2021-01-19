<?php

namespace App\Components\Users\Participant\Mutators\DTO\Traits;

use App\Components\Users\Participant\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Users\Participant\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $participantMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function participantMutator__(): Mutator
    {
        if (!$this->participantMutator instanceof Mutator) {
            $this->participantMutator = app()->make(Mutator::class);
        }

        return $this->participantMutator;
    }
}
