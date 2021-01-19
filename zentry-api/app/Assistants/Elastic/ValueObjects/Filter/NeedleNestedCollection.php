<?php

namespace App\Assistants\Elastic\ValueObjects\Filter;

use App\Assistants\Elastic\ValueObjects\Filter\Contracts\NeedlePresenter;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class NeedleNestedCollection
 *
 * @package App\Assistants\Elastic\ValueObjects\Search
 */
final class NeedleNestedCollection implements NeedlePresenter
{
    public const MUST = 'must';

    public const SHOULD = 'should';

    public const AVAILABLE_SEARCH_TYPES = [
        self::MUST,
        self:: SHOULD,
    ];

    /**
     * @var Collection
     */
    private Collection $needleCollections;

    /**
     * @var string
     */
    private $searchType;

    /**
     * @param Collection $needleCollections
     * @param string     $searchType
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Collection $needleCollections, string $searchType)
    {
        $this->needleCollections = $needleCollections;
        $this->setSearchType($searchType);
    }

    /**
     * @return string
     */
    public function _searchType(): string
    {
        return $this->searchType;
    }

    /**
     * @param string $searchType
     *
     * @return NeedleNestedCollection
     * @throws InvalidArgumentException
     */
    private function setSearchType(string $searchType): NeedleNestedCollection
    {
        if (!in_array($searchType, self::AVAILABLE_SEARCH_TYPES, true)) {
            throw new InvalidArgumentException("Wrong search type");
        }

        $this->searchType = $searchType;

        return $this;
    }

    /**
     * @return Collection
     */
    public function _needleCollections(): Collection
    {
        return $this->needleCollections;
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'bool' => [
                $this->_searchType() => $this->_needleCollections()->map(
                    function (NeedleCollection $needleCollection) {
                        return $needleCollection->present();
                    }
                )->values()->toArray(),
            ],
        ];
    }
}
