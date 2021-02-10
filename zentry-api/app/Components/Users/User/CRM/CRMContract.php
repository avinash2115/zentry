<?php

namespace App\Components\Users\User\CRM;

use App\Components\CRM\Source\SourceContract;
use App\Components\Users\ValueObjects\CRM\Config\Config;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Interface CRMContract
 *
 * @package App\Components\Users\User\Storage
 */
interface CRMContract extends CRMReadonlyContract
{
    /**
     * @param Config $value
     *
     * @return CRMContract
     */
    public function changeConfig(Config $value): CRMContract;

    /**
     * @return CRMContract
     */
    public function enable(): CRMContract;

    /**
     * @return CRMContract
     */
    public function disable(): CRMContract;

    /**
     * @return CRMContract
     */
    public function markNotified(): CRMContract;

    /**
     * @return CRMContract
     */
    public function clearNotified(): CRMContract;

    /**
     * @param Identity $identity
     *
     * @return SourceContract
     * @throws NotFoundException
     */
    public function sourcesByIdentity(Identity $identity): SourceContract;

    /**
     * @param Identity $identity
     *
     * @return SourceContract
     * @throws NotFoundException
     */
    public function sourcesByCRM(Identity $identity): SourceContract;
}
