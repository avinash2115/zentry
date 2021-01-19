<?php

namespace App\Convention\Entities\Traits;

use App\Convention\Entities\Contracts\IdentifiableContract;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Illuminate\Support\Collection;

/**
 * Trait CollectibleTrait
 *
 * @package App\Convention\Entities\Traits
 */
trait CollectibleTrait
{
    /**
     * Convert doctrine collection to system collection
     *
     * @param DoctrineCollection|null $collection
     * @param bool                    $verifyKeyId
     *
     * @return Collection
     */
    protected function doctrineCollectionToCollection(
        ?DoctrineCollection $collection,
        bool $verifyKeyId = true
    ): Collection {
        $result = collect($collection === null ? [] : $collection->toArray());

        if ($verifyKeyId && $this->checkKeyByAvailability($result)) {
            return $result->keyBy(
                function (IdentifiableContract $item) {
                    return (string)$item->identity();
                }
            );
        }

        return $result;
    }

    /**
     * Check if we can convert keyBy id
     *
     * @param Collection $collection
     *
     * @return bool
     */
    protected function checkKeyByAvailability(Collection $collection): bool
    {
        $firstItem = $collection->first();

        return $firstItem instanceof IdentifiableContract && $collection->keys()->first() !== $firstItem->identity();
    }
}