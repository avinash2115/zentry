<?php

namespace App\Components\Providers\ProviderService\Mutators\DTO\Traits;

use App\Components\Providers\ProviderService\Mutators\DTO\Mutator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait MutatorTrait
 *
 * @package App\Components\Providers\ProviderService\Mutators\DTO\Traits
 */
trait MutatorTrait
{
    /**
     * @var Mutator | null
     */
    private ?Mutator $providerMutator = null;

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function providerMutator__(): Mutator
    {
        if (!$this->providerMutator instanceof Mutator) {
            $this->providerMutator = app()->make(Mutator::class);
        }

        return $this->providerMutator;
    }
}
