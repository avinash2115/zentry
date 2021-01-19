<?php

namespace App\Assistants\Elastic\ValueObjects\Filter;

use App\Assistants\Elastic\ValueObjects\Filter\Contracts\NeedlePresenter;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class NeedleCollection
 *
 * @package App\Assistants\Elastic\ValueObjects\Search
 */
final class NeedleCollection implements NeedlePresenter
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
    private Collection $needles;

    /**
     * @var string
     */
    private string $searchType;

    /**
     * @param Collection $needles
     * @param string     $searchType
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Collection $needles, string $searchType)
    {
        $this->needles = $needles;
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
     * @return NeedleCollection
     * @throws InvalidArgumentException
     */
    private function setSearchType(string $searchType): NeedleCollection
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
    public function _needles(): Collection
    {
        return $this->needles;
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'bool' => [
                $this->_searchType() => $this->_needles()->map(
                    static function (Needle $needle) {
                        return $needle->present();
                    }
                )->values()->toArray(),
            ],
        ];
    }
}
