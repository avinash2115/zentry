<?php

namespace App\Components\Providers\ProviderService;

use InvalidArgumentException;

/**
 * Interface ProviderContract
 *
 * @package App\Components\Providers\ProviderService
 */
interface ProviderContract extends ProviderReadonlyContract
{
    /**
     * @param string $value
     *
     * @return ProviderContract
     * @throws InvalidArgumentException
     */
    public function changeName(string $value): ProviderContract;
    public function changeCode(string $value): ProviderContract;
    


}
