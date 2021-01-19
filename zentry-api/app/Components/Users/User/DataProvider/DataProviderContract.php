<?php

namespace App\Components\Users\User\DataProvider;

use App\Convention\ValueObjects\Config\Config;

/**
 * Interface DataProviderContract
 *
 * @package App\Components\Users\User\Storage
 */
interface DataProviderContract extends DataProviderReadonlyContract
{
    /**
     * @param Config $value
     *
     * @return DataProviderContract
     */
    public function changeConfig(Config $value): DataProviderContract;

    /**
     * @return DataProviderContract
     */
    public function enable(): DataProviderContract;

    /**
     * @return DataProviderContract
     */
    public function disable(): DataProviderContract;

    /**
     * @return DataProviderContract
     */
    public function notAuthorized(): DataProviderContract;
}
