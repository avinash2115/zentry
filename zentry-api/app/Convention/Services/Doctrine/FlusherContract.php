<?php

namespace App\Convention\Services\Doctrine;

use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface FlusherContract
 *
 * @package App\Convention\Services\Doctrine
 */
interface FlusherContract
{
    /**
     * @return void
     */
    public function open(): void;

    /**
     * @return void
     * @throws InvalidArgumentException
     */
    public function flush(): void;

    /**
     * @param bool $fake
     *
     * @return void
     * @throws BindingResolutionException|InvalidArgumentException
     */
    public function commit(bool $fake = false): void;

    /**
     * @return void
     */
    public function rollback(): void;

    /**
     * @param string|null $objectName
     *
     * @return void
     * @throws RuntimeException
     */
    public function clear(string $objectName = null): void;
}
