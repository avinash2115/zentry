<?php

namespace App\Components\Users\PasswordReset\Repository;

use App\Components\Users\PasswordReset\PasswordResetContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Class PasswordResetRepositoryMemory
 *
 * @package App\Components\Users\PasswordReset\Repository
 */
class PasswordResetRepositoryMemory extends AbstractMemoryRepository implements PasswordResetRepositoryContract
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
    public function byIdentity(Identity $identity): PasswordResetContract
    {
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof PasswordResetContract) {
            throw new NotFoundException();
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?PasswordResetContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(PasswordResetContract $passwordReset): PasswordResetContract
    {
        $this->register($passwordReset);

        return $passwordReset;
    }

    /**
     * @inheritDoc
     */
    public function register(PasswordResetContract $passwordReset): PasswordResetContract
    {
        $this->collector->put($passwordReset->identity()->toString(), $passwordReset);

        return $passwordReset;
    }

    /**
     * @inheritDoc
     */
    public function destroy(PasswordResetContract $passwordReset): bool
    {
        if ($this->collector->has($passwordReset->identity()->toString())) {
            $this->collector->forget($passwordReset->identity()->toString());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): PasswordResetRepositoryContract
    {
        return $this;
    }
}
