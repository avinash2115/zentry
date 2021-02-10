<?php

namespace App\Components\Users\User\Repository;

use App\Components\Users\User\UserContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class UserRepositoryMemory
 *
 * @package App\Components\Users\User\Repository
 */
class UserRepositoryMemory extends AbstractMemoryRepository implements UserRepositoryContract
{
    /**
     */
    public function __construct()
    {
        parent::__construct(self::CLASS_NAME, self::ALIAS);
    }

    /**
     * @inheritDoc
     */
    public function byIdentity(Identity $identity): UserContract
    {
        $entity = $this->collector->get($identity->toString());

        $this->refreshBuilder();

        if (!$entity instanceof UserContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?UserContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(UserContract $user): UserContract
    {
        $this->collector->put($user->identity()->toString(), $user);

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function destroy(UserContract $user): bool
    {
        if ($this->collector->has($user->identity()->toString())) {
            $this->collector->forget($user->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByIds(array $ids, bool $contains = true): UserRepositoryContract
    {
        $this->filteredResults = $this->collector()->filter(
            function (UserReadonlyContract $user) use ($ids, $contains) {
                return $contains ? in_array($user->identity()->toString(), $ids, true) : !in_array(
                    $user->identity()->toString(),
                    $ids,
                    true
                );
            }
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmails(array $emails, bool $contains = true): UserRepositoryContract
    {
        $this->filteredResults = $this->collector()->filter(
            function (UserReadonlyContract $user) use ($emails, $contains) {
                return $contains ? in_array($user->email(), $emails, true) : !in_array(
                    $user->email(),
                    $emails,
                    true
                );
            }
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isExists(string $email): bool
    {
        return $this->collector()->first(
                function (UserContract $user) use ($email) {
                    return $user->email() === $email;
                }
            ) instanceof UserContract;
    }

    /**
     * @inheritDoc
     */
    public function filterByStorageDrivers(
        array $emails,
        bool $contains = true,
        bool $enabled = true
    ): UserRepositoryContract {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByDataProviders(array $drivers, bool $contains = true): UserRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByDataProvidersStatuses(array $statuses, bool $contains = true): UserRepositoryContract
    {
        return $this;
    }
}
