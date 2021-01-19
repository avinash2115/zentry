<?php

namespace App\Components\Users\ValueObjects\Participant\Goal;

use App\Convention\Contracts\Arrayable;

/**
 * Class Meta
 *
 * @package App\Components\Users\ValueObjects\Participant\Goal
 */
final class Meta implements Arrayable
{
    /**
     * @var array
     */
    private array $meta;

    /**
     * @param array  $meta
     */
    public function __construct(array $meta = [])
    {
        $this->meta = $meta;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->meta;

    }
}
