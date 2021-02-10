<?php

namespace App\Components\Users\Services\PasswordReset;

use App\Components\Users\Exceptions\ResetPassword\TokenExpiredException;
use App\Components\Users\PasswordReset\PasswordResetDTO;
use App\Components\Users\PasswordReset\PasswordResetReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;

/**
 * Interface PasswordResetServiceContract
 *
 * @package App\Components\Users\Services\PasswordReset
 */
interface PasswordResetServiceContract extends FilterableContract
{
    /**
     * @param string $id
     *
     * @return PasswordResetServiceContract
     * @throws TokenExpiredException|NotFoundException|BindingResolutionException
     */
    public function workWith(string $id): PasswordResetServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return PasswordResetReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): PasswordResetReadonlyContract;

    /**
     * @return PasswordResetDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): PasswordResetDTO;

    /**
     * @param array $data
     *
     * @return PasswordResetServiceContract
     * @throws NonUniqueResultException|NotFoundException|BindingResolutionException
     */
    public function create(array $data): PasswordResetServiceContract;

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
     * @param array $data
     *
     * @return PasswordResetServiceContract
     * @throws PropertyNotInit|NotFoundException|BindingResolutionException|NonUniqueResultException|NoResultException
     */
    public function setNewPassword(array $data): PasswordResetServiceContract;

    /**
     * @return PasswordResetServiceContract
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function remove(): PasswordResetServiceContract;
}
