<?php

namespace App\Assistants\Search\Search;

use App\Assistants\Transformers\Contracts\Presenter\RelationshipsContract;
use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use Illuminate\Support\Collection;

/**
 * Class SearchDTO
 *
 * @package App\Assistants\Search\Search
 */
class SearchDTO implements PresenterContract, RelationshipsContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    public string $_type = 'search';

    /**
     * @var Collection | null
     */
    public ?Collection $relationships;

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect();
    }

    /**
     * @inheritDoc
     */
    public function nested(): Collection
    {
        return $this->relationships instanceof Collection ? $this->relationships : collect();
    }

    /**
     * @inheritDoc
     */
    public function required(): Collection
    {
        return collect();
    }
}
