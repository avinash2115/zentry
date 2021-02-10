<?php

namespace App\Components\CRM\Services\SyncLog;

use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\CRM\SyncLog\SyncLogDTO;
use App\Components\CRM\SyncLog\SyncLogReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface SyncLogServiceContract
 *
 * @package App\Components\CRM\Services\SyncLog
 */
interface SyncLogServiceContract extends FilterableContract
{
    /**
     * @param string $id
     *
     * @return SyncLogServiceContract
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws BindingResolutionException
     */
    public function workWith(string $id): SyncLogServiceContract;

    /**
     * @return SyncLogReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): SyncLogReadonlyContract;

    /**
     * @return SyncLogDTO
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function dto(): SyncLogDTO;

    /**
     * @param CRMReadonlyContract $crm
     * @param string              $crmEntityType
     *
     * @return SyncLogServiceContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     * @throws RuntimeException
     */
    public function create(CRMReadonlyContract $crm, string $crmEntityType): SyncLogServiceContract;

    /**
     * @return Collection
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function listRO(): Collection;

    /**
     * @return SyncLogServiceContract
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function remove(): SyncLogServiceContract;
}
