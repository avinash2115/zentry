<?php

namespace App\Assistants\Elastic\ValueObjects;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class Mapping
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class Mapping
{
    public const NEEDLE_TYPE_IDENTIFIER = 'identifier';

    public const TYPE_STRING = 'string';

    public const TYPE_LONG_TEXT = 'long_text';

    public const TYPE_DATE = 'date';

    public const TYPE_ARRAY = 'array';

    public const TYPE_NUMBER = 'number';

    public const AVAILABLE_TYPES = [
        self::NEEDLE_TYPE_IDENTIFIER,
        self::TYPE_STRING,
        self::TYPE_LONG_TEXT,
        self::TYPE_DATE,
        self::TYPE_ARRAY,
        self::TYPE_NUMBER,
    ];

    /**
     * @var string
     */
    private string $attribute;

    /**
     * @var string
     */
    private string $type;

    /**
     * @param string $attribute
     * @param string $type
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $attribute, string $type)
    {
        $this->setAttribute($attribute);
        $this->setType($type);
    }

    /**
     * @return string
     */
    public function attribute(): string
    {
        return $this->attribute;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function isAttribute(string $attribute): bool
    {
        return $this->attribute() === $attribute;
    }

    /**
     * @param string $attribute
     *
     * @return Mapping
     */
    private function setAttribute(string $attribute): Mapping
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isType(string $type): bool
    {
        return $this->type() === $type;
    }

    /**
     * @param string $type
     *
     * @return Mapping
     * @throws InvalidArgumentException
     */
    private function setType(string $type): Mapping
    {
        if (!in_array($type, self::AVAILABLE_TYPES, true)) {
            throw new InvalidArgumentException('Type is not allowed');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function asSortAttribute(): string
    {
        switch ($this->type()) {
            case self::TYPE_ARRAY:
                return "{$this->attribute}.keyword";
            case self::NEEDLE_TYPE_IDENTIFIER:
            case self::TYPE_LONG_TEXT:
            case self::TYPE_DATE:
                return $this->attribute;
            case self::TYPE_STRING:
            case self::TYPE_NUMBER:
            default:
                return "{$this->attribute}.sort";
        }
    }
}
