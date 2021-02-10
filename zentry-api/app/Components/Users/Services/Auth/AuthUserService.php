<?php

namespace App\Components\Users\Services\Auth;

use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\Mutators\DTO\Mutator;
use App\Components\Users\User\UserContract;
use App\Components\Users\User\UserDTO;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use UnexpectedValueException;

/**
 * Class AuthUserService
 *
 * @package App\Components\Users\Services\Auth
 */
class AuthUserService implements AuthUserServiceContract
{
    use UserServiceTrait;
    use DeviceServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var UserReadonlyContract | null
     */
    private ?UserReadonlyContract $entity;

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
     * @inheritDoc
     */
    public function workWith(string $identity): AuthUserServiceContract
    {
        $entity = $this->userService__()->workWith($identity)->readonly();

        return $this->setEntity($entity);
    }

    /**
     * @return UserContract
     * @throws PropertyNotInit
     */
    private function _entity(): UserContract
    {
        if (!$this->entity instanceof UserContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return AuthUserServiceContract
     */
    private function setEntity(UserReadonlyContract $user): AuthUserServiceContract
    {
        $this->entity = $user;

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
    public function readonly(): UserReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): UserDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    /**
     * @inheritDoc
     */
    public function getAuthIdentifier(): string
    {
        return $this->_entity()->identity()->toString();
    }

    /**
     * @inheritDoc
     */
    public function getAuthPassword(): string
    {
        return $this->_entity()->password();
    }

    /**
     * @inheritDoc
     */
    public function getRememberTokenName(): string
    {
        return 'rememberToken';
    }

    /**
     * @inheritDoc
     */
    public function retrieveByIdentity(string $identity): AuthUserServiceContract
    {
        return $this->workWith($identity);
    }

    /**
     * @inheritDoc
     */
    public function workWithByFilters(Collection $filters): AuthUserServiceContract
    {
        if (!$filters->has('email')) {
            throw new UnexpectedValueException('Email is missed');
        }

        $user = $this->userService__()->workWithByEmail($filters->get('email', ''))->readonly();

        $this->setEntity($user);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function connect(ConnectingPayload $payload): AuthUserServiceContract
    {
        $this->deviceService__()->create($this->userService__()->readonly(), $payload);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getJWTIdentifier(): string
    {
        return (string)$this->getAuthIdentifier();
    }

    /**
     * @inheritdoc
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getRememberToken(): string
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritdoc
     */
    public function setRememberToken($value): void
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }
}
