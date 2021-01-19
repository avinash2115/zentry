<?php

namespace App\Assistants\Transformers\Tests\Unit;

use App\Assistants\Transformers\Contracts\Presenter\LinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelatedLinksContract;
use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\LinkParameters;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use Illuminate\Support\Collection;

/**
 * Class NestedExamplePresenterClass
 *
 * @package App\Assistants\Transformers\Tests\Unit
 */
class NestedExamplePresenterClass implements PresenterContract, LinksContract, RelatedLinksContract, RelationshipsContract
{
    use PresenterTrait;

    /**
     * @var array
     */
    public array $attributes = [];

    /**
     * @var array
     */
    public array $links = [];

    /**
     * @var string
     */
    public string $type = 'example';

    /**
     * @var array
     */
    public array $relations = [];

    /**
     * @return Collection
     */
    public function attributes(): Collection
    {
        return collect($this->attributes);
    }

    /**
     * @param array $array
     *
     * @return NestedExamplePresenterClass
     */
    public function setAttributes(array $array = []): NestedExamplePresenterClass
    {
        $this->attributes = $array;

        return $this;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return 'b863a151-4rf2-8899-9b8c-fa69b4a5656c';
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @param array $array
     *
     * @return NestedExamplePresenterClass
     */
    public function setLinks(array $array = []): NestedExamplePresenterClass
    {
        $this->links = $array;

        return $this;
    }

    /**
     * @param LinkParameters $linkParameters
     *
     * @return Collection
     */
    public function relatedData(LinkParameters $linkParameters): Collection
    {
        return collect($this->links);
    }

    /**
     * @inheritDoc
     */
    public function data(LinkParameters $linkParameters): Collection
    {
        return collect($this->links);
    }

    /**
     * @return Collection
     */
    public function routeParameters(): Collection
    {
        return collect([]);
    }

    /**
     * @return Collection
     */
    public function nested(): Collection
    {
        return collect($this->relations);
    }

    /**
     * @param array $array
     *
     * @return NestedExamplePresenterClass
     */
    public function setRelationships(array $array = []): NestedExamplePresenterClass
    {
        $this->relations = $array;

        return $this;
    }

    /**
     * @return Collection
     */
    public function required(): Collection
    {
        return collect([]);
    }

    /**
     * To array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
