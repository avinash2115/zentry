<?php

namespace App\Convention\DTO\Mutators;

/**
 * Interface SimplifiedDTOContract
 *
 * @package App\Convention\DTO\Mutators
 */
interface SimplifiedDTOContract
{
    /**
     * @return bool
     */
    public function simplifiedMutation(): bool;

    /**
     * @return bool
     */
    public function fullMutation(): bool;

    /**
     * @return bool
     */
    public function isSimplifiedMutation(): bool;
}