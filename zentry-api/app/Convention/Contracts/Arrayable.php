<?php
namespace App\Convention\Contracts;

/**
 * Interface Arrayable
 *
 * @package App\Convention\Contracts
 */
interface Arrayable
{
    /**
     * @return array
     */
    public function toArray(): array;
}