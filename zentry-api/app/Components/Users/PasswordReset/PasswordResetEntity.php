<?php

namespace App\Components\Users\PasswordReset;

use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use DateInterval;
use DateTime;
use Exception;

/**
 * Class PasswordResetEntity
 *
 * @package App\Components\Users\PasswordReset
 */
class PasswordResetEntity implements PasswordResetContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

    /**
     * @var DateTime
     */
    private DateTime $ttl;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @param Identity             $identity
     * @param UserReadonlyContract $user
     *
     * @throws Exception
     */
    public function __construct(Identity $identity, UserReadonlyContract $user)
    {
        $this->setIdentity($identity);
        $this->setTTL();
        $this->setCreatedAt();
        $this->setUpdatedAt();

        $this->setUser($user);
    }

    /**
     * @return PasswordResetContract
     * @throws Exception
     */
    private function setTTL(): PasswordResetContract
    {
        $this->ttl = (new DateTime())->add(new DateInterval('PT' . config('auth.reset_password_ttl') . 'M'));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isExpired(): bool
    {
        $now = new DateTime();

        return $now > $this->TTL();
    }

    /**
     * @inheritdoc
     */
    public function TTL(): DateTime
    {
        return $this->ttl;
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return PasswordResetEntity
     */
    private function setUser(UserReadonlyContract $user): PasswordResetEntity
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function user(): UserReadonlyContract
    {
        return $this->user;
    }
}
