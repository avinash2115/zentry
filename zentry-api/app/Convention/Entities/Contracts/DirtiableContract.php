<?php

namespace App\Convention\Entities\Contracts;

/**
 * Interface DirtiableContract
 *
 * @package App\Convention\Entities\Contracts
 */
interface DirtiableContract
{
    /**
     * @return bool
     */
    public function isDirty(): bool;
}