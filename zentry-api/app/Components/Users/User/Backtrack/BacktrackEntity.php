<?php

namespace App\Components\Users\User\Backtrack;

use App\Components\Users\User\UserContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class BacktrackEntity
 *
 * @package App\Components\Users\User\Backtrack
 */
class BacktrackEntity implements BacktrackContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

    /**
     * @var int
     */
    private int $backward;

    /**
     * @var int
     */
    private int $forward;

    /**
     * @var UserContract
     */
    private UserContract $user;

    /**
     * BacktrackEntity constructor.
     *
     * @param Identity     $identity
     * @param UserContract $user
     * @param int          $backward
     *
     * @throws Exception
     */
    public function __construct(Identity $identity, UserContract $user, int $backward = self::DEFAULT_BACKWARD)
    {
        $this->setIdentity($identity);
        $this->user = $user;
        $this->setBackward($backward);
        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function backward(): int
    {
        return $this->backward;
    }

    /**
     * @param int $backward
     *
     * @return BacktrackEntity
     */
    private function setBackward(int $backward): BacktrackEntity
    {
        if ($backward < 1) {
            throw new InvalidArgumentException('Backward value should be more than 0');
        }

        $this->backward = $backward;

        return $this;
    }
}
