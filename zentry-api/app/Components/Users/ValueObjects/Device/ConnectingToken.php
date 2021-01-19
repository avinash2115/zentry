<?php

namespace App\Components\Users\ValueObjects\Device;

use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use DateTime;
use Exception;

/**
 * Class ConnectingToken
 * @package App\Components\Users\ValueObjects\Device
 */
final class ConnectingToken implements IdentifiableContract
{
    use IdentifiableTrait;

    /**
     * @var Identity
     */
    private Identity $userIdentity;

    /**
     * @var DateTime
     */
    private DateTime $createdAt;

    /**
     * @var int
     */
    private int $ttl = 600;

    /**
     * @var int
     */
    private int $retentionTtl = 604800;

    /**
     * @param Identity $userIdentity
     * @param DateTime $createAt
     */
    public function __construct(Identity $userIdentity, DateTime $createAt)
    {
        $this->setIdentity(IdentityGenerator::next());
        $this->setUserIdentity($userIdentity);
        $this->setCreatedAt($createAt);
    }

    /**
     * @return Identity
     */
    public function userIdentity(): Identity
    {
        return $this->userIdentity;
    }

    /**
     * @param Identity $userIdentity
     */
    private function setUserIdentity(Identity $userIdentity): void
    {
        $this->userIdentity = $userIdentity;
    }

    /**
     * @return DateTime
     */
    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    private function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function ttl(): int
    {
        return $this->ttl;
    }

    /**
     * @return int
     */
    public function retentionTtl(): int
    {
        return $this->retentionTtl;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isExpired(): bool  {
        return (new DateTime())->getTimestamp() - $this->createdAt()->getTimestamp() >= $this->ttl();
    }
}
