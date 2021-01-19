<?php

namespace App\Convention\ValueObjects;

use App\Convention\Contracts\Arrayable;

/**
 * Class Tag
 *
 * @package App\Convention\ValueObjects
 */
final class Tag implements Arrayable
{
    /**
     * @var string
     */
    private string $tag;

    /**
     * Tag constructor.
     *
     * @param string $tag
     */
    public function __construct(string $tag)
    {
        $this->setTag($tag);
    }

    /**
     * @param string $tag
     */
    private function setTag(string $tag): void
    {
        $this->tag = $tag;
    }

    /**
     * @return string
     */
    public function tag(): string
    {
        return $this->tag;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'tag' => $this->tag,
        ];
    }
}
