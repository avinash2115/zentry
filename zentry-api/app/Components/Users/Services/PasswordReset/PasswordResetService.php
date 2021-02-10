<?php

namespace App\Components\Users\Services\PasswordReset;

use App\Assistants\Events\EventRegistry;
use App\Components\Users\Exceptions\ResetPassword\TokenExpiredException;
use App\Components\Users\PasswordReset\Events\Created;
use App\Components\Users\PasswordReset\Mutators\DTO\Mutator;
use App\Components\Users\PasswordReset\Repository\PasswordResetRepositoryContract;
use App\Components\Users\PasswordReset\PasswordResetContract;
use App\Components\Users\PasswordReset\PasswordResetDTO;
use App\Components\Users\PasswordReset\PasswordResetReadonlyContract;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class PasswordResetService
 *
 * @package App\Components\Users\Services\PasswordReset
 */
class PasswordResetService implements PasswordResetServiceContract
{
    use UserServiceTrait;
    use FilterableTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var PasswordResetContract | null
     */
    private ?PasswordResetContract $entity = null;

    /**
     * @var PasswordResetRepositoryContract | null
     */
    private ?PasswordResetRepositoryContract $repository = null;

    /**
     * @return self
     * @throws BindingResolutionException
     */
    private function setMutator(): self
    {
        if (!$this->mutator instanceof Mutator) {
            $this->mutator = app()->make(Mutator::class);
        }

        return $this;
    }

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function _mutator(): Mutator
    {
        $this->setMutator();

        return $this->mutator;
    }

    /**
     * @return self
     * @throws BindingResolutionException
     */
    private function setRepository(): self
    {
        if (!$this->repository instanceof PasswordResetRepositoryContract) {
            $this->repository = app()->make(PasswordResetRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @return PasswordResetRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): PasswordResetRepositoryContract
    {
        $this->setRepository();

        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): PasswordResetServiceContract
    {
        $this->applyFilters([]);

        $entity = $this->_repository()->byIdentity(new Identity($id));

        if ($entity->isExpired()) {
            $this->_repository()->destroy($entity);
            throw new TokenExpiredException('Token expired');
        }

        $this->setEntity($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function identity(): Identity
    {
        return $this->_entity()->identity();
    }

    /**
     * @inheritDoc
     */
    public function readonly(): PasswordResetReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): PasswordResetDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @param PasswordResetContract $passwordReset
     *
     * @return PasswordResetServiceContract
     */
    private function setEntity(PasswordResetContract $passwordReset): PasswordResetServiceContract
    {
        $this->entity = $passwordReset;

        return $this;
    }

    /**
     * @return PasswordResetContract
     * @throws PropertyNotInit
     */
    private function _entity(): PasswordResetContract
    {
        if (!$this->entity instanceof PasswordResetContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): PasswordResetServiceContract
    {
        $entity = $this->make($data);

        $this->setEntity($entity);

        $this->_repository()->persist($this->_entity());

        app()->make(EventRegistry::class)->register(new Created($this->readonly()));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->handleFilters($this->filters());

        $results = $this->_repository()->getAll();

        $this->applyFilters([]);

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->sortByDesc(
            function (PasswordResetReadonlyContract $passwordReset) {
                return $passwordReset->createdAt();
            }
        )->map(
            function (PasswordResetContract $passwordReset) {
                return $this->_mutator()->toDTO($passwordReset);
            }
        );
    }

    /**
     * @param array $filters
     *
     * @throws BindingResolutionException
     */
    private function handleFilters(array $filters): void
    {
        if (Arr::has($filters, 'users')) {
            $needleScopes = Arr::get($filters, 'users.collection', []);
            $isContains = filter_var(Arr::get($filters, 'users.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByUsersIds($needleScopes, $isContains);
        }
    }

    /**
     * @param array $data
     *
     * @return PasswordResetContract
     * @throws NotFoundException|NonUniqueResultException|BindingResolutionException
     */
    private function make(array $data): PasswordResetContract
    {
        $user = $this->userService__()->workWithByEmail(Arr::get($data, 'email', ''))->readonly();

        return app()->make(
            PasswordResetContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function setNewPassword(array $data): PasswordResetServiceContract
    {
        $passwordReset = $this->_entity();

        $this->userService__()->workWith($passwordReset->user()->identity())->change(
            [
                'password' => Arr::get($data, 'password', ''),
                'password_repeat' => Arr::get($data, 'password_repeat', ''),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): PasswordResetServiceContract
    {
        $entity = $this->_entity();

        $this->_repository()->destroy($entity);

        return $this;
    }
}
