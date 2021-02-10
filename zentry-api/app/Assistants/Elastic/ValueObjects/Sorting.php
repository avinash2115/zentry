<?php

namespace App\Assistants\Elastic\ValueObjects;

use App\Components\Sessions\Session\Mutators\DTO\Mutator;
use App\Convention\Contracts\Arrayable;
use InvalidArgumentException;

/**
 * Class Sorting
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class Sorting implements Arrayable
{
    public const ASC = 'asc';
    public const DESC = 'desc';
    public const AVAILABLE_DIRECTIONS = [
        self::ASC,
        self::DESC,
    ];

    public const SHOULD_BE_SORTED = [
        Mutator::TYPE,
    ];

    /**
     * @var Mapping
     */
    private Mapping $mapping;

    /**
     * @var string
     */
    private string $direction;

    /**
     * @param Mapping $mapping
     * @param string  $direction
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Mapping $mapping, string $direction = self::ASC)
    {
        $this->mapping = $mapping;
        $this->setDirection($direction);
    }

    /**
     * @return Mapping
     */
    public function mapping(): Mapping
    {
        return $this->mapping;
    }

    /**
     * @return string
     */
    public function direction(): string
    {
        return $this->direction;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [$this->mapping()->asSortAttribute() => $this->direction()];
    }

    /**
     * @param string $direction
     *
     * @return Sorting
     * @throws InvalidArgumentException
     */
    private function setDirection(string $direction): Sorting
    {
        if (!in_array($direction, self::AVAILABLE_DIRECTIONS, true)) {
            throw new InvalidArgumentException("Direction {$direction} is not allowed");
        }

        $this->direction = $direction;

        return $this;
    }
}
