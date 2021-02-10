<?php

namespace App\Components\Users\Services\Login\Token;

use App\Components\Users\Login\Token\TokenDTO;
use App\Components\Users\Login\Token\TokenReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface TokenServiceContract
 *
 * @package App\Components\Users\Services\Login\Token
 */
interface TokenServiceContract extends FilterableContract
{
    /**
     * @param string $id
     *
     * @return TokenServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException
     */
    public function workWith(string $id): TokenServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return TokenReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): TokenReadonlyContract;

    /**
     * @return TokenDTO
     * @throws PropertyNotInit|BindingResolutionException|RuntimeException
     */
    public function dto(): TokenDTO;

    /**
     * @param UserReadonlyContract $user
     * @param string               $referer
     *
     * @return TokenServiceContract
     * @throws PropertyNotInit|BindingResolutionException|NonUniqueResultException
     */
    public function create(UserReadonlyContract $user, string $referer): TokenServiceContract;

    /**
     * @throws BindingResolutionException
     */
    public function listRO(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     */
    public function list(): Collection;

    /**
     * @return TokenServiceContract
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function remove(): TokenServiceContract;

    /**
     * @param string $referer
     *
     * @return string
     * @throws PropertyNotInit|BindingResolutionException|PermissionDeniedException
     */
    public function login(string $referer): string;
}
