<?php

namespace App\Components\Services\Service\Mutators\DTO\Traits;

use App\Components\Services\Service\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Services\Service\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $serviceMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function serviceMutator__(): Mutator
    {
        if (!$this->serviceMutator instanceof Mutator) {
            $this->serviceMutator = app()->make(Mutator::class);
        }

        return $this->serviceMutator;
    }
}
