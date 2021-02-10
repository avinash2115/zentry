<?php

namespace App\Assistants\Elastic\ValueObjects;

use Arr;
use DateTime;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class Body
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class Body
{
    /**
     * @var array
     */
    private array $body;

    /**
     * @param array    $body
     * @param Mappings $mappings
     *
     * @throws InvalidArgumentException
     */
    public function __construct(array $body, Mappings $mappings)
    {
        $this->setBody($body, $mappings);
    }

    /**
     * @return array
     */
    public function body(): array
    {
        return $this->body;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return collect($this->body());
    }

    /**
     * @param array    $body
     * @param Mappings $mappings
     *
     * @return Body
     * @throws InvalidArgumentException
     */
    private function setBody(array $body, Mappings $mappings): self
    {
        if (Arr::has($body, Document::DOCUMENT_TYPE_KEY)) {
            throw new InvalidArgumentException("Attribute '_type' is reserved and cannot be included into the body.");
        }

        $this->body = $this->convertValues($body, $mappings);

        return $this;
    }

    /**
     * @param array    $body
     * @param Mappings $mappings
     *
     * @return array
     */
    private function convertValues(array $body, Mappings $mappings): array
    {
        array_walk(
            $body,
            static function (&$value, $attribute) use ($mappings) {
                if (!$mappings->validate($attribute, $value)) {
                    $invalidValue = json_encode($value, JSON_THROW_ON_ERROR);
                    throw new InvalidArgumentException(
                        "The mappings returned false on validation attribute: '{$attribute}', value: {$invalidValue}"
                    );
                }

                switch (true) {
                    case $value instanceof DateTime :
                        $value = dateTimeFormatted($value);
                    break;
                    case is_array($value):
                    break;
                    default:
                        $value = (string)$value;
                    break;
                }
            }
        );

        return $body;
    }
}
