<?php

namespace App\Convention\DTO\Mutators\Traits;

/**
 * Trait SimplifiedDTOServiceTrait
 *
 * @package App\Convention\DTO\Mutators\Traits
 */
trait SimplifiedDTOServiceTrait
{
    /**
     * @inheritDoc
     */
    public function simplifiedMutation(): bool
    {
        $this->_mutator()->simplifiedMutation();

        return $this->isSimplifiedMutation();
    }

    /**
     * @inheritDoc
     */
    public function fullMutation(): bool
    {
        $this->_mutator()->fullMutation();

        return !$this->isSimplifiedMutation();
    }

    /**
     * @inheritDoc
     */
    public function isSimplifiedMutation(): bool
    {
        return $this->_mutator()->isSimplifiedMutation();
    }
}
