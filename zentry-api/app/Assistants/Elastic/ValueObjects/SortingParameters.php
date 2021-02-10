<?php

namespace App\Assistants\Elastic\ValueObjects;

use InvalidArgumentException;

/**
 * Class SortingParameters
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class SortingParameters
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @var string
     */
    private string $direction;

    /**
     * @param string $key
     * @param string $direction
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $key, string $direction = Sorting::ASC)
    {
        $this->key = $key;
        $this->setDirection($direction);
    }

    /**
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function direction(): string
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     *
     * @return SortingParameters
     * @throws InvalidArgumentException
     */
    private function setDirection(string $direction): SortingParameters
    {
        if (!in_array($direction, Sorting::AVAILABLE_DIRECTIONS, true)) {
            throw new InvalidArgumentException("Direction {$direction} is not allowed");
        }

        $this->direction = $direction;

        return $this;
    }
}
