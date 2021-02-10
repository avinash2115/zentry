<?php

namespace App\Assistants\Files\Drivers\ValueObjects;

/**
 * Class Quota
 *
 * @package App\Assistants\Files\Drivers\ValueObjects
 */
class Quota
{
    /**
     * @var int
     */
    private int $used;

    /**
     * @var int
     */
    private int $capacity;

    /**
     * Quota constructor.
     *
     * @param int $used
     * @param int $capacity
     */
    public function __construct(int $used, int $capacity)
    {
        $this->used = $used;
        $this->capacity = $capacity;
    }

    /**
     * @return int
     */
    public function used(): int
    {
        return $this->used;
    }

    /**
     * @return int
     */
    public function capacity(): int
    {
        return $this->capacity;
    }
}
