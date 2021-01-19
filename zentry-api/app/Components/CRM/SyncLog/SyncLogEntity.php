<?php

namespace App\Components\CRM\SyncLog;

use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Users\User\CRM\CRMContract;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Convention\Entities\Traits\HasCreatedAtTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class SyncLogEntity
 *
 * @package App\Components\CRM\SyncLog
 */
class SyncLogEntity implements SyncLogContract
{
    use IdentifiableTrait;
    use HasCreatedAtTrait;

    /**
     * @var CRMContract
     */
    private CRMContract $crm;

    /**
     * @var string
     */
    private string $type;

    /**
     * SyncLogEntity constructor.
     *
     * @param Identity    $identity
     * @param CRMContract $crm
     * @param string      $type
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(Identity $identity, CRMContract $crm, string $type)
    {
        $this->setIdentity($identity);
        $this->setType($type);

        $this->crm = $crm;

        $this->setCreatedAt();
    }

    /**
     * @inheritDoc
     */
    public function crm(): CRMReadonlyContract
    {
        return $this->crm;
    }

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return SyncLogEntity
     * @throws InvalidArgumentException
     */
    private function setType(string $type): SyncLogEntity
    {
        if (!in_array($type, CRMImportableContract::CRM_ENTITY_TYPES_AVAILABLE, true)) {
            throw new InvalidArgumentException("Type {$type} is not allowed");
        }

        $this->type = $type;

        return $this;
    }
}
