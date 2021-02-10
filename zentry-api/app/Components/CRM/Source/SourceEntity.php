<?php

namespace App\Components\CRM\Source;

use App\Components\Users\User\CRM\CRMContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class SourceEntity
 *
 * @package App\Components\CRM\Source
 */
abstract class SourceEntity implements SourceContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

    /**
     * @var string
     */
    private string $direction;

    /**
     * @var string
     */
    private string $sourceId;

    /**
     * @var string
     */
    private string $ownerId;

    /**
     * @var CRMContract
     */
    private CRMContract $crm;

    /**
     * SourceEntity constructor.
     *
     * @param Identity              $identity
     * @param CRMContract           $crm
     * @param CRMImportableContract $owner
     * @param string                $sourceId
     * @param string                $direction
     *
     * @throws Exception
     */
    public function __construct(Identity $identity, CRMContract $crm, CRMImportableContract $owner, string $sourceId, string $direction = self::DIRECTION_IN)
    {
        $this->setIdentity($identity);

        $this->crm = $crm;
        $this->setOwner($owner);

        $this->setSourceID($sourceId);
        $this->setDirection($direction);

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function sourceId(): string
    {
        return $this->sourceId;
    }

    /**
     * @param string $id
     *
     * @return SourceEntity
     * @throws InvalidArgumentException
     */
    private function setSourceID(string $id): SourceEntity
    {
        if (strEmpty($id)) {
            throw new InvalidArgumentException("SourceID can't be empty");
        }

        $this->sourceId = $id;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function direction(): string
    {
        return $this->direction;
    }

    /**
     * @param string $direction
     *
     * @return SourceEntity
     * @throws InvalidArgumentException
     */
    private function setDirection(string $direction): SourceEntity
    {
        if (!in_array($direction, self::AVAILABLE_DIRECTIONS, true)) {
            throw new InvalidArgumentException("Type {$direction} is not allowed");
        }

        $this->direction = $direction;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function crm(): CRMReadonlyContract
    {
        return $this->crm;
    }

    /**
     * @return CRMImportableContract
     */
    abstract public function owner(): CRMImportableContract;

    /**
     * @param CRMImportableContract $entity
     *
     * @return SourceEntity
     */
    abstract public function setOwner($entity): SourceEntity;
}
