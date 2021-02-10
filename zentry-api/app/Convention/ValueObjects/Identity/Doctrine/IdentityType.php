<?php
namespace App\Convention\ValueObjects\Identity\Doctrine;

use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Psr\Log\InvalidArgumentException;

/**
 * Class IdentityType
 *
 * @package App\Convention\ValueObjects\Identity\Doctrine
 */
class IdentityType extends Type
{
    /**
     * Name of identity
     */
    const NAME = 'identity';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param array $fieldDeclaration
     * @param AbstractPlatform $platform
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // TODO: need full implementations of UUID
        return 'IDENTITY';
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return Identity|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
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
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return null|string
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Identity) {
            return (string) $value;
        }

        throw ConversionException::conversionFailed($value, static::NAME);
    }
}