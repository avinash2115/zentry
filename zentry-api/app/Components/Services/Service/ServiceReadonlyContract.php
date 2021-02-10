<?php

namespace App\Components\Services\Service;

use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;

/**
 * Interface ServiceReadonlyContract
 *
 * @package App\Components\Services\Service
 */
interface ServiceReadonlyContract extends IdentifiableContract, TimestampableContract, CRMImportableContract
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
     * @return string
     */
    public function category(): string;

     /**
     * @return string
     */
    public function status(): string;

     /**
     * @return string
     */
    public function actions(): string;

    /**
     * @return UserReadonlyContract
     */
    public function user(): UserReadonlyContract;
}
