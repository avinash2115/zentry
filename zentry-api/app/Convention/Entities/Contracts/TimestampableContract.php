<?php
namespace App\Convention\Entities\Contracts;

use DateTime;

/**
 * Interface TimestampableContract
 *
 * @package App\Convention\Entities\Contracts
 */
interface TimestampableContract extends HasCreatedAt
{
    /**
     * @return DateTime
     */
    public function updatedAt(): DateTime;
}