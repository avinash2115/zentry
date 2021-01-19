<?php

namespace App\Components\Users\ValueObjects\CRM\Doctrine;

use App\Components\Users\ValueObjects\CRM\Config\Config;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

/**
 * Class ConfigType
 */
class ConfigType extends JsonType
{
    /**
     * Name of identity
     */
    public const NAME = 'crm_config';

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
     * @return Config
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Config) {
            return $value;
        }

        if ($value === null) {
            return new Config([]);
        }

        return new Config(parent::convertToPHPValue($value, $platform), '', true);
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

        if ($value instanceof Config) {
            return parent::convertToDatabaseValue($value->toArray(), $platform);
        }

        throw ConversionException::conversionFailed($value, static::NAME);
    }
}
