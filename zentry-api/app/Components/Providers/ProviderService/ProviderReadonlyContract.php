<?php

namespace App\Components\Providers\ProviderService;

use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface ProviderReadonlyContract
 *
 * @package App\Components\Providers\ProviderService
 */
interface ProviderReadonlyContract extends IdentifiableContract, TimestampableContract, CRMImportableContract
{
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function code(): string;


    /**
     * @return UserReadonlyContract
     */
    public function user(): UserReadonlyContract;
}
