<?php

namespace App\Components\Sessions\ValueObjects\Doctrine;

use App\Components\Sessions\Services\Traits\SessionHelperTrait;
use App\Components\Sessions\ValueObjects\Geo;
use App\Convention\ValueObjects\Tags;
use Arr;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;
use InvalidArgumentException;

/**
 * Class GeoType
 *
 * @package App\Components\Sessions\ValueObjects\Doctrine
 */
class GeoType extends JsonType
{
    /**
     * Name of identity
     */
    public const NAME = 'geo';

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
     * @return Geo|null
     * @throws ConversionException
     * @throws InvalidArgumentException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Geo) {
            return $value;
        }

        if ($value === null) {
            return null;
        }

        $geo = parent::convertToPHPValue($value, $platform);

        return new Geo(Arr::get($geo, 'lng', 0), Arr::get($geo, 'lat', 0), Arr::get($geo, 'place', ''));
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
        if ($value === null) {
            return null;
        }

        if ($value instanceof Geo) {
            return parent::convertToDatabaseValue($value->toArray(), $platform);
        }

        throw ConversionException::conversionFailed($value, static::NAME);
    }
}
