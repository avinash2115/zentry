<?php
namespace App\Convention\Entities\Contracts;

use DateTime;

/**
 * Interface ArchivableContract
 *
 * @package App\Convention\Entities\Contracts
 */
interface ArchivableContract extends ArchivableReadonlyContract
{
    /**
     * @return bool
     */
    public function archive(): bool;

    /**
     * @return bool
     */
    public function restore(): bool;
}