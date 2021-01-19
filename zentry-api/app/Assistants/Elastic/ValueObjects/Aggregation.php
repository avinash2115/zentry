<?php

namespace App\Assistants\Elastic\ValueObjects;

use Illuminate\Support\Collection;

/**
 * Class Aggregation
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class Aggregation
{
    /**
     * @var string
     */
    private string $attribute;

    /**
     * @var Collection
     */
    private Collection $values;

    /**
     * @param string $attribute
     * @param array  $values
     */
    public function __construct(string $attribute, array $values = [])
    {
        $this->setAttribute($attribute);
        $this->setValues($values);
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
     * @return Aggregation
     */
    private function setAttribute(string $attribute): Aggregation
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * @return Collection
     */
    public function values(): Collection
    {
        return $this->values;
    }

    /**
     * @param array $values
     *
     * @return Aggregation
     */
    private function setValues(array $values): Aggregation
    {
        $this->values = collect($values)->filter(
            static function (string $value) {
                return $value !== '';
            }
        )->values();

        return $this;
    }
}
