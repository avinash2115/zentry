<?php

namespace App\Components\CRM\Services\SyncLog\Traits;

use App\Components\CRM\Services\SyncLog\SyncLogServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait SyncLogServiceTrait
 *
 * @package App\Components\CRM\Services\SyncLog\Traits
 */
trait SyncLogServiceTrait
{
    /**
     * @var SyncLogServiceContract | null
     */
    private ?SyncLogServiceContract $syncLogService__ = null;

    /**
     * @return SyncLogServiceContract
     * @throws BindingResolutionException
     */
    private function syncLogService__(): SyncLogServiceContract
    {
        if (!$this->syncLogService__ instanceof SyncLogServiceContract) {
            $this->syncLogService__ = app()->make(SyncLogServiceContract::class);
        }

        return $this->syncLogService__;
    }
}
