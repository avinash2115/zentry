<?php

namespace App\Convention\ValueObjects\Contracts;

/**
 * Interface Stringable
 *
 * @package App\Convention\ValueObjects\Contracts
 */
interface Stringable
{
    /**
     * @return string
     */
    public function toString(): string;
}
