<?php

namespace App\Components\Sessions\Session\Poi\Participant\Mutators\DTO\Traits;

use App\Components\Sessions\Session\Poi\Participant\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Sessions\Session\Poi\Participant\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $poiParticipantMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function poiParticipantMutator__(): Mutator
    {
        if (!$this->poiParticipantMutator instanceof Mutator) {
            $this->poiParticipantMutator = app()->make(Mutator::class);
        }

        return $this->poiParticipantMutator;
    }
}
