<?php

namespace App\Components\Users\User\Storage;

use InvalidArgumentException;

/**
 * Interface StorageContract
 *
 * @package App\Components\Users\User\Storage
 */
interface StorageContract extends StorageReadonlyContract
{

    /**
     * @param string $name
     *
     * @return StorageContract
     * @throws InvalidArgumentException
     */
    public function changeName(string $name): StorageContract;

    /**
     * @return StorageContract
     */
    public function enable(): StorageContract;

    /**
     * @return StorageContract
     */
    public function disable(): StorageContract;

    /**
     * @param int $used
     *
     * @return StorageContract
     */
    public function changeUsed(int $used): StorageContract;

    /**
     * @param int $capacity
     *
     * @return StorageContract
     */
    public function changeCapacity(int $capacity): StorageContract;
}
