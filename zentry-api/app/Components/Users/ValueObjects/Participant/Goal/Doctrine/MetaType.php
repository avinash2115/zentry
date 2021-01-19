<?php

namespace App\Components\Users\ValueObjects\Participant\Goal\Doctrine;

use App\Components\Users\ValueObjects\Participant\Goal\Meta;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

/**
 * Class MetaType
 */
class MetaType extends JsonType
{
    public const NAME = 'participant_goal_meta';

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
     * @return Meta
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Meta) {
            return $value;
        }

        if ($value === null) {
            return new Meta();
        }

        return new Meta(parent::convertToPHPValue($value, $platform));
    }

    /**
     * @param mixed            $value
     * @param AbstractPlatform $platform
     *
     * @return string
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null || (is_array($value) && count($value) === 0)) {
            return parent::convertToDatabaseValue([], $platform);
        }

        if ($value instanceof Meta) {
            return parent::convertToDatabaseValue($value->toArray(), $platform);
        }

        throw ConversionException::conversionFailed($value, static::NAME);
    }
}
