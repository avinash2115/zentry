<?php

namespace App\Convention\Generators\Identity;

use App\Convention\ValueObjects\Identity\Identity;

/**
 * Interface IdentityGeneratorContract
 *
 * @package App\Convention\Generators\Identity
 */
interface IdentityGeneratorContract
{
    /**
     * @return Identity
     */
    public static function next(): Identity;

    /**
     * @param string $string
     * @return bool
     */
    public static function isValid(string $string): bool;
}
