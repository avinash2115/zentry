<?php
namespace App\Components\Share\Contracts;

use App\Components\Share\ValueObjects\Payload;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use InvalidArgumentException;

/**
 * Interface SharableContract
 *
 * @package App\Components\Share\Contracts
 */
interface SharableContract
{
    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return string[]
     */
    public function types(): array;

    /**
     * @return Payload
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     */
    public function payload(): Payload;

    /**
     * @return bool
     * @throws PropertyNotInit
     */
    public function isWrapped(): bool;
}
