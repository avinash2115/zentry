<?php

namespace App\Convention\DTO\Mutators\Traits;

/**
 * Trait SimplifiedDTOTrait
 *
 * @package App\Convention\DTO\Mutators\Traits
 */
trait SimplifiedDTOTrait
{
    /**
     * @var bool
     */
    protected bool $simplifiedMutation = false;

    /**
     * @return bool
     */
    public function simplifiedMutation(): bool
    {
        $this->simplifiedMutation = true;

        return $this->isSimplifiedMutation();
    }

    /**
     * @return bool
     */
    public function fullMutation(): bool
    {
        $this->simplifiedMutation = false;

        return !$this->isSimplifiedMutation();
    }

    /**
     * @return bool
     */
    public function isSimplifiedMutation(): bool
    {
        return $this->simplifiedMutation;
    }
}