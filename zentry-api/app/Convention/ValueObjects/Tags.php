<?php

namespace App\Convention\ValueObjects;

use App\Convention\Contracts\Arrayable;
use Arr;
use Illuminate\Support\Collection;

/**
 * Class Tags
 *
 * @package App\Convention\ValueObjects
 */
final class Tags implements Arrayable
{
    /**
     * @var Collection
     */
    private Collection $tags;

    /**
     * @param array $tags
     */
    public function __construct(array $tags)
    {
        $this->setTags(array_filter($tags));
    }

    /**
     * @param array $tags
     */
    private function setTags(array $tags): void
    {
        $this->tags = collect($tags)->map(
            static function (array $tag) {
                return new Tag(Arr::get($tag, 'tag'));
            }
        );
    }

    /**
     * @return Collection
     */
    public function tags(): Collection
    {
        return $this->tags;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return $this->tags->map(
            static function (Tag $tag) {
                return $tag->toArray();
            }
        )->filter()->toArray();
    }
}
