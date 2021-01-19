<?php

namespace App\Components\Users\User\Poi;

use App\Components\Users\User\UserContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class PoiEntity
 *
 * @package App\Components\Users\User\Poi
 */
class PoiEntity implements PoiContract
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
     * @param Identity     $identity
     * @param UserContract $user
     * @param int          $backward
     * @param int          $forward
     *
     * @throws Exception|InvalidArgumentException
     */
    public function __construct(Identity $identity, UserContract $user, int $backward = self::DEFAULT_BACKWARD, int $forward = self::DEFAULT_FORWARD)
    {
        $this->setIdentity($identity);
        $this->user = $user;
        $this->setBackward($backward);
        $this->setForward($forward);
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
     * @return PoiEntity
     * @throws InvalidArgumentException
     */
    private function setBackward(int $backward): PoiEntity
    {
        if ($backward < 1) {
            throw new InvalidArgumentException('Backward value should be more than 0');
        }

        $this->backward = $backward;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function forward(): int
    {
        return $this->forward;
    }

    /**
     * @param int $forward
     *
     * @return PoiEntity
     * @throws InvalidArgumentException
     */
    private function setForward(int $forward): PoiEntity
    {
        if ($forward < 1) {
            throw new InvalidArgumentException('Forward value should be more than 0');
        }

        $this->forward = $forward;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function duration(): int
    {
        return $this->forward() + $this->backward();
    }
}
