<?php
namespace App\Convention\Entities\Contracts;

use DateTime;

/**
 * Interface HasCreatedAt
 *
 * @package App\Convention\Entities\Contracts
 */
interface HasCreatedAt
{
    /**
     * @return DateTime
     */
    public function createdAt(): DateTime;
}