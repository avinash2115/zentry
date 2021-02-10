<?php

namespace App\Components\Share\Services\Shared;

use App\Components\Share\Contracts\SharableContract;
use App\Components\Share\Shared\Mutators\DTO\Mutator;
use App\Components\Share\Shared\Repository\SharedRepositoryContract;
use App\Components\Share\Shared\SharedContract;
use App\Components\Share\Shared\SharedDTO;
use App\Components\Share\Shared\SharedReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class SharedService
 *
 * @package App\Components\Share\Services\Shared
 */
class SharedService implements SharedServiceContract
{
    /**
     * @var Mutator|null
     */
    private ?Mutator $mutator = null;

    /**
     * @var SharedRepositoryContract|null
     */
    private ?SharedRepositoryContract $repository = null;

    /**
     * @var SharedContract|null
     */
    private ?SharedContract $entity = null;

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
        if (!$this->repository instanceof SharedRepositoryContract) {
            $this->repository = app()->make(SharedRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @return SharedRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): SharedRepositoryContract
    {
        $this->setRepository();

        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): SharedServiceContract
    {
        $this->setEntity($this->_repository()->byIdentity(new Identity($id)));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWithSharable(SharableContract $sharable): SharedServiceContract
    {
        $entity = $this->_repository()->filterByPayload($sharable->payload())->getOne();

        if (!$entity instanceof SharedContract) {
            throw new NotFoundException();
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
    public function readonly(): SharedReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): SharedDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function create(SharableContract $sharable): SharedServiceContract
    {
        if (!$sharable->isWrapped()) {
            throw new RuntimeException("Sharable type {$sharable->type()} is not ready.");
        }

        $entity = $this->_repository()->filterByPayload($sharable->payload())->getOne();

        if (!$entity instanceof SharedContract) {
            $entity = $this->make($sharable);
            $this->_repository()->persist($entity);
        }

        $this->setEntity($entity);

        return $this;
    }

    /**
     * @param SharableContract $entity
     *
     * @return SharedContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     */
    private function make(SharableContract $entity): SharedContract
    {
        return app()->make(SharedContract::class, [
            'identity' => IdentityGenerator::next(),
            'type' => $entity->type(),
            'payload' => $entity->payload()
        ]);
    }

    /**
     * @inheritDoc
     */
    public function remove(): SharedServiceContract
    {
        $this->_repository()->destroy($this->_entity());

        return $this;
    }

    /**
     * @param SharedContract $entity
     *
     * @return SharedService
     */
    private function setEntity(SharedContract $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return SharedContract
     * @throws PropertyNotInit
     */
    private function _entity(): SharedContract
    {
        if (!$this->entity instanceof SharedContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }
}
