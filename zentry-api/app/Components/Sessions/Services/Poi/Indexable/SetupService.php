<?php

namespace App\Components\Sessions\Services\Poi\Indexable;

use App\Assistants\Elastic\Contracts\Indexable\SetupableContract;
use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Mapping;
use App\Assistants\Elastic\ValueObjects\Mappings;
use App\Assistants\Elastic\ValueObjects\Type;
use App\Assistants\Search\Agency\Services\Searchable;
use App\Assistants\Search\Agency\Services\Traits\SearchableTrait;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\Poi\Mutators\DTO\Mutator;
use Illuminate\Support\Collection;

/**
 * Class SetupService
 *
 * @package App\Components\Sessions\Services\Poi\Indexable
 */
class SetupService implements SetupableContract, Searchable
{
    use SearchableTrait;
    use SessionServiceTrait;

    /**
     * @inheritDoc
     */
    public function asType(): Type
    {
        return new Type(Mutator::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function asMappings(Index $index): Mappings
    {
        switch ($index->index()) {
            case Index::INDEX_ENTITIES:
                return new Mappings(
                    collect(
                        [
                            new Mapping('user_id', Mapping::TYPE_STRING),
                            new Mapping('session_id', Mapping::TYPE_STRING),
                            new Mapping('name', Mapping::TYPE_STRING),
                            new Mapping('words', Mapping::TYPE_LONG_TEXT),
                            new Mapping('tags', Mapping::TYPE_ARRAY),
                        ]
                    )
                );
            default:
                throw new IndexNotSupported($index);
        }
    }

    /**
     * @inheritDoc
     */
    public function verifyResults(Collection $results, ?Collection $filters = null): Collection
    {
        if (!$filters instanceof Collection) {
            $filters = collect();
        }

        $filters->put(
            'poi',
            [
                'collection' => $results->toArray(),
            ]
        );

        $this->sessionService__()->applyFilters($filters->toArray());

        return $this->sessionService__()->list();
    }
}
