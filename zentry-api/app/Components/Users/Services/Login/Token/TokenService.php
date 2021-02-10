<?php

namespace App\Components\Users\Services\Login\Token;

use App\Components\Users\Login\Token\Mutators\DTO\Mutator;
use App\Components\Users\Login\Token\Repository\TokenRepositoryContract;
use App\Components\Users\Login\Token\TokenContract;
use App\Components\Users\Login\Token\TokenDTO;
use App\Components\Users\Login\Token\TokenReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Flusher;
use Illuminate\Contracts\Container\BindingResolutionException;
use Arr;
use Illuminate\Support\Collection;

/**
 * Class TokenService
 *
 * @package App\Components\Users\Services\LoginToken
 */
class TokenService implements TokenServiceContract
{
    use UserServiceTrait;
    use FilterableTrait;
    use AuthServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var TokenContract | null
     */
    private ?TokenContract $entity = null;

    /**
     * @var TokenRepositoryContract | null
     */
    private ?TokenRepositoryContract $repository = null;

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
        if (!$this->repository instanceof TokenRepositoryContract) {
            $this->repository = app()->make(TokenRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @return TokenRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): TokenRepositoryContract
    {
        $this->setRepository();

        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): TokenServiceContract
    {
        $this->applyFilters([]);

        $this->setEntity($this->_repository()->byIdentity(new Identity($id)));

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
    public function readonly(): TokenReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): TokenDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @param TokenContract $loginToken
     *
     * @return TokenServiceContract
     */
    private function setEntity(TokenContract $loginToken): TokenServiceContract
    {
        $this->entity = $loginToken;

        return $this;
    }

    /**
     * @return TokenContract
     * @throws PropertyNotInit
     */
    private function _entity(): TokenContract
    {
        if (!$this->entity instanceof TokenContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @inheritDoc
     */
    public function create(UserReadonlyContract $user, string $referer): TokenServiceContract
    {
        $entity = $this->_repository()->filterByUsersIds([$user->identity()])->getOne();

        if ($entity instanceof TokenContract) {
            $this->setEntity($entity)->remove();
            Flusher::flush();
        }

        $entity = $this->make($user, $referer);
        $this->_repository()->persist($entity);
        $this->setEntity($entity);

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
            static function (TokenReadonlyContract $loginToken) {
                return $loginToken->createdAt();
            }
        )->map(
            function (TokenContract $loginToken) {
                return $this->_mutator()->toDTO($loginToken);
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
     * @param UserReadonlyContract $user
     * @param string               $referer
     *
     * @return TokenContract
     * @throws BindingResolutionException
     */
    private function make(UserReadonlyContract $user, string $referer): TokenContract
    {
        return app()->make(
            TokenContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'referer' => $referer,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function remove(): TokenServiceContract
    {
        $entity = $this->_entity();

        $this->_repository()->destroy($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function login(string $referer): string
    {
        if ($this->_entity()->referer() !== $referer) {
            throw new PermissionDeniedException();
        }

        $token = $this->authService__()->tokenFromUser($this->_entity()->user());

        $this->remove();

        return $token;
    }
}