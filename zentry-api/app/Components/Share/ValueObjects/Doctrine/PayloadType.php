<?php

namespace App\Components\Share\ValueObjects\Doctrine;

use App\Components\Share\ValueObjects\Payload;
use Arr;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

/**
 * Class PayloadType
 */
class PayloadType extends JsonType
{
    /**
     * Name of identity
     */
    public const NAME = 'shared_payload';

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
     * @return Payload|null
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof Payload) {
            return $value;
        }

        $payload = parent::convertToPHPValue($value, $platform);

        return new Payload(
            Arr::get($payload, 'pattern'), Arr::get($payload, 'parameters'), Arr::get($payload, 'methods', [])
        );
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
        if ($value instanceof Payload) {
            return parent::convertToDatabaseValue($value->toArray(), $platform);
        }

        throw ConversionException::conversionFailed($value, static::NAME);
    }
}
