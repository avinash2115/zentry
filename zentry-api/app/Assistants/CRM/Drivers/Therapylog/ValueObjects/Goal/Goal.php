<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Goal;

use \Arr;
use \InvalidArgumentException;

/**
 * Class Goal
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Goal
 */
class Goal
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var array
     */
    private array $raw;

    /**
     * Caseload constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        if (strEmpty(Arr::get($args, 'id', ''))) {
            throw new InvalidArgumentException('ID must be present');
        }

        $this->id = Arr::get($args, 'id', '');
        $this->setName(Arr::get($args, 'name', ''));
        $this->raw = $args;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Goal
     */
    private function setName(string $name): Goal
    {
        if (strEmpty($name)) {
            throw new InvalidArgumentException('Name must be present');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function raw(): array
    {
        return $this->raw;
    }
}
