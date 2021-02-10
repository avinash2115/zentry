<?php

namespace App\Components\Users\Login\Token;

use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\HasCreatedAtTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class TokenEntity
 *
 * @package App\Components\Users\Login\Token
 */
class TokenEntity implements TokenContract
{
    use IdentifiableTrait;
    use HasCreatedAtTrait;

    /**
     * @var string
     */
    private string $referer;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @param Identity             $identity
     * @param UserReadonlyContract $user
     * @param string               $referer
     *
     * @throws Exception
     */
    public function __construct(Identity $identity, UserReadonlyContract $user, string $referer)
    {
        $this->setIdentity($identity);
        $this->setReferer($referer)->setUser($user);

        $this->setCreatedAt();
    }

    /**
     * @param string $referer
     *
     * @return TokenEntity
     * @throws Exception
     */
    private function setReferer(string $referer): TokenEntity
    {
        if (strEmpty($referer)) {
            throw new InvalidArgumentException("Referer can't be empty");
        }

        $this->referer = $referer;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function referer(): string
    {
        return $this->referer;
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return TokenEntity
     */
    private function setUser(UserReadonlyContract $user): TokenEntity
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
