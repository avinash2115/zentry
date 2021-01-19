<?php

namespace App\Components\Services\Service;

use InvalidArgumentException;

/**
 * Interface ServiceContract
 *
 * @package App\Components\Services\Service
 */
interface ServiceContract extends ServiceReadonlyContract
{
    /**
     * @param string $value
     *
     * @return ServiceContract
     * @throws InvalidArgumentException
     */
    public function changeName(string $value): ServiceContract;
    public function changeCode(string $value): ServiceContract;
    public function changeCategory(string $value): ServiceContract;
    public function changeStatus(string $value): ServiceContract;
    public function changeActions(string $value): ServiceContract;


}
