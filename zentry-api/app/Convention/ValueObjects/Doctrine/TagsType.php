<?php

namespace App\Convention\ValueObjects\Doctrine;

use App\Convention\ValueObjects\Tags;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

/**
 * Class TagsType
 */
class TagsType extends JsonType
{
    /**
     * Name of identity
     */
    public const NAME = 'tags';

    /**
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return Tags|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Tags) {
            return $value;
        }

        return new Tags(parent::convertToPHPValue($value, $platform));
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return null|string
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Tags) {
            return parent::convertToDatabaseValue($value->toArray(), $platform);
        }

        throw ConversionException::conversionFailed($value, static::NAME);
    }
}
