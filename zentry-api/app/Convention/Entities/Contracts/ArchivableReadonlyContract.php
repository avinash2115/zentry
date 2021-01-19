<?php

namespace App\Convention\Entities\Contracts;

use DateTime;

/**
 * Interface ArchivableContract
 *
 * @package App\Convention\Entities\Contracts
 */
interface ArchivableReadonlyContract
{
    /**
     * @return DateTime|null
     */
    public function archivedAt(): ?DateTime;

    /**
     * @return bool
     */
    public function isArchived(): bool;
}