<?php

namespace App\Convention\Generators\Identity;

use App\Convention\ValueObjects\Identity\Identity;
use Ramsey\Uuid\Uuid;

/**
 * Class IdentityGenerator
 *
 * @package App\Convention\Generators\Identity
 */
class IdentityGenerator implements IdentityGeneratorContract
{
    /**
     * @inheritDoc
     */
    public static function next(): Identity
    {
        return new Identity(Uuid::uuid4()->toString());
    }

    /**
     * @inheritDoc
     */
    public static function isValid(string $string): bool
    {
        return Uuid::isValid($string);
    }
}
