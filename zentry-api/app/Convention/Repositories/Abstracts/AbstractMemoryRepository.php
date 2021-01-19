<?php

namespace App\Convention\Repositories\Abstracts;

use App\Convention\Repositories\Contracts\RepositoryContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class AbstractMemoryRepository
 *
 * @package App\Convention\Repositories\Abstracts
 */
abstract class AbstractMemoryRepository implements RepositoryContract
{
    /**
     * @var Collection
     */
    protected Collection $collector;

    /**
     * @var Collection|null
     */
    protected ?Collection $filteredResults = null;

    /**
     * @var string
     */
    private string $className;

    /**
     * @var string
     */
    private string $alias;

    /**
     * @param string $className
     * @param string $alias
     */
    public function __construct(string $className, string $alias)
    {
        $this->collector = collect([]);
        $this->className = $className;
        $this->alias = $alias;
    }

    /**
     * @return string
     * @throws UnexpectedValueException
     */
    protected function getClassName(): string
    {
        if (strEmpty($this->className)) {
            throw new UnexpectedValueException('Class name cannot be empty');
        }

        return $this->className;
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    protected function getAlias(): string
    {
        if (strEmpty($this->alias)) {
            throw new InvalidArgumentException('Alias cannot be empty');
        }

        return $this->alias;
    }

    /**
     * @inheritdoc
     */
    public function join(
        string $relation,
        string $alias = null,
        string $type = self::INNER_JOIN_TYPE,
        bool $addSelect = false
    ): string {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getAll(?callable $callback = null): Collection
    {
        $result = $this->collector();
        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setMaxResults(int $numberOfResults): RepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOffset(int $offset): RepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sortBy(array $data, string $alias = null): RepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $result = $this->collector()->count();
        $this->refreshBuilder();

        return $result;
    }

    /**
     * @return Collection
     */
    protected function collector(): Collection
    {
        return $this->filteredResults instanceof Collection ? $this->filteredResults : $this->collector;
    }

    /**
     *
     */
    protected function refreshBuilder(): void
    {
        $this->filteredResults = null;
    }
}
