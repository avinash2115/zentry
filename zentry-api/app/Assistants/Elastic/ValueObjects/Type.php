<?php

namespace App\Assistants\Elastic\ValueObjects;

/**
 * Class Type
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class Type
{
    /**
     * @var string
     */
    private string $type;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->setType($type);
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
     * @return Type
     */
    private function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
