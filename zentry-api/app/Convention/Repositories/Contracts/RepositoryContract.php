<?php

namespace App\Convention\Repositories\Contracts;

use App\Convention\Exceptions\Logic\NotImplementedException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use UnexpectedValueException;

/**
 * Interface RepositoryContract
 *
 * @package App\Convention\Repositories\Contracts
 */
interface RepositoryContract
{
    public const INNER_JOIN_TYPE = 'inner';

    public const LEFT_JOIN_TYPE = 'left';

    /**
     * @param string      $relation
     * @param string|null $alias
     * @param string      $type
     * @param bool        $addSelect
     *
     * @return string
     * @throws BindingResolutionException|NotImplementedException
     */
    public function join(
        string $relation,
        string $alias = null,
        string $type = self::INNER_JOIN_TYPE,
        bool $addSelect = false
    ): string;

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function getAll(): Collection;

    /**
     * @param int $numberOfResults
     *
     * @return RepositoryContract
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    public function setMaxResults(int $numberOfResults): RepositoryContract;

    /**
     * @param int $offset
     *
     * @return RepositoryContract
     */
    public function setOffset(int $offset): RepositoryContract;

    /**
     * @param array       $data
     * @param string|null $alias
     *
     * @return RepositoryContract
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    public function sortBy(array $data, string $alias = null): RepositoryContract;

    /**
     * @return int
     * @throws BindingResolutionException|NoResultException|NonUniqueResultException
     */
    public function count(): int;
}
