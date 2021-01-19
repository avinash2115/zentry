<?php

namespace App\Components\Sessions\Session\Poi\Mutators\DTO\Traits;

use App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Sessions\Session\Poi\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $poiMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function poiMutator__(): Mutator
    {
        if (!$this->poiMutator instanceof Mutator) {
            $this->poiMutator = app()->make(Mutator::class);
        }

        return $this->poiMutator;
    }
}
