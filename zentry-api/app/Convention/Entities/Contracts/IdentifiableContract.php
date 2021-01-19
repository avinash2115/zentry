<?php
namespace App\Convention\Entities\Contracts;

use App\Convention\ValueObjects\Identity\Identity;

/**
 * Interface IdentifiableContract
 *
 * @package App\Convention\Entities\Contracts
 */
interface IdentifiableContract
{
    /**
     * @return Identity
     */
    public function identity(): Identity;
}
