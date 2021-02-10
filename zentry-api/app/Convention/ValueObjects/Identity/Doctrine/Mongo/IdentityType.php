<?php

namespace App\Convention\ValueObjects\Identity\Doctrine\Mongo;

use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ODM\MongoDB\Types\Type;
use InvalidArgumentException;

/**
 * Class IdentityType
 *
 * @package App\Convention\ValueObjects\Identity\Doctrine\Mongo
 */
class IdentityType extends Type
{
    /**
     * Name of identity
     */
    public const NAME = 'identity';

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     *
     * @return Identity|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value): ?Identity
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Identity) {
            return $value;
        }

        try {
            $uuid = new Identity($value);
        } catch (InvalidArgumentException $exception) {
            throw ConversionException::conversionFailed($value, static::NAME);
        }

        return $uuid;
    }

    /**
     * @param Identity|string $value
     *
     * @return Identity|null
     * @throws ConversionException
     */
    public static function convertToPHPValueStatic($value): ?Identity
    {
        if ($value instanceof Identity) {
            return $value;
        }

        try {
            $uuid = new Identity($value);
        } catch (InvalidArgumentException $exception) {
            throw ConversionException::conversionFailed($value, static::NAME);
        }

        return $uuid;
    }

    /**
     * @return string
     */
    public function closureToPHP(): string
    {
        return '$return = \App\Convention\ValueObjects\Identity\Doctrine\Mongo\IdentityType::convertToPHPValueStatic($value);';
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed $value
     *
     * @return null | string
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Identity) {
            return $value->toString();
        }

        if (is_string($value)) {
            try {
                new Identity($value);
            } catch (InvalidArgumentException $exception) {
                throw ConversionException::conversionFailed($value, static::NAME);
            }

            return $value;
        }

        throw ConversionException::conversionFailed((string)$value, static::NAME);
    }
}
