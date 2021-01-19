<?php

namespace App\Components\Users\Login\Token\Repository;

use App\Components\Users\Login\Token\Mutators\DTO\Mutator;
use App\Components\Users\Login\Token\TokenContract;
use App\Components\Users\Login\Token\TokenEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Contracts\RepositoryContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Interface TokenRepositoryContract
 *
 * @package App\Components\Users\Login\Token\Repository
 */
interface TokenRepositoryContract extends RepositoryContract
{
    public const CLASS_NAME = TokenEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return TokenContract
     * @throws NotFoundException|BindingResolutionException
     */
    public function byIdentity(Identity $identity): TokenContract;

    /**
     * @return TokenContract|null
     * @throws NonUniqueResultException|BindingResolutionException
     */
    public function getOne(): ?TokenContract;

    /**
     * @param TokenContract $entity
     *
     * @return TokenContract
     * @throws BindingResolutionException
     */
    public function persist(TokenContract $entity): TokenContract;

    /**
     * @param TokenContract $entity
     *
     * @return bool
     * @throws BindingResolutionException
     */
    public function destroy(TokenContract $entity): bool;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return TokenRepositoryContract
     * @throws BindingResolutionException
     */
    public function filterByUsersIds(array $ids, bool $contains = true): TokenRepositoryContract;
}
