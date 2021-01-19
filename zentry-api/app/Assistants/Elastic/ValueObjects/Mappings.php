<?php

namespace App\Assistants\Elastic\ValueObjects;

use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Illuminate\Support\Collection;
use UnexpectedValueException;

/**
 * Class Mappings
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class Mappings
{
    /**
     * @var Collection
     */
    private Collection $collection;

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->setCollection($collection);
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->collection;
    }

    /**
     * @param Collection $collection
     *
     * @return Mappings
     */
    private function setCollection(Collection $collection): Mappings
    {
        $collection->each(
            static function (Mapping $item) {
            }
        );

        $this->collection = $collection->values();

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return Mapping
     * @throws UnexpectedValueException
     */
    public function mapping(string $attribute): Mapping
    {
        $mapping = $this->collection()->first(
            static function (Mapping $mapping) use ($attribute) {
                return $mapping->isAttribute($attribute);
            }
        );

        if (!$mapping instanceof Mapping) {
            throw new UnexpectedValueException("Mapping not found for {$attribute}");
        }

        return $mapping;
    }

    /**
     * @param string $attribute
     *
     * @return int
     * @throws UnexpectedValueException
     */
    public function mappingWeight(string $attribute): int
    {
        $weight = $this->collection()->search(
            static function (Mapping $mapping) use ($attribute) {
                return $mapping->isAttribute($attribute);
            }
        );

        if ($weight === false) {
            throw new UnexpectedValueException("Mapping weight not found for {$attribute}");
        }

        return (int)$weight;
    }

    /**
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function validate(string $attribute, $value): bool
    {
        $mapping = $this->collection()->first(
            static function (Mapping $mapping) use ($attribute, $value) {
                if ($mapping->isAttribute($attribute)) {
                    switch ($mapping->type()) {
                        case Mapping::NEEDLE_TYPE_IDENTIFIER:
                        case Mapping::TYPE_LONG_TEXT:
                        case Mapping::TYPE_STRING:
                            return $value === null || is_string($value) || is_int(
                                    $value
                                ) || $value instanceof Identity;
                        case Mapping::TYPE_ARRAY:
                            return is_array($value);
                        case Mapping::TYPE_DATE:
                            return $value instanceof DateTime;
                        case Mapping::TYPE_NUMBER:
                            return is_numeric($value);
                    }
                }

                return false;
            }
        );

        return $mapping instanceof Mapping;
    }
}
