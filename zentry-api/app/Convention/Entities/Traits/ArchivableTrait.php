<?php

namespace App\Convention\Entities\Traits;

use DateTime;
use Exception;

/**
 * Trait ArchivableTrait
 *
 * @package App\Convention\Entities\Traits
 */
trait ArchivableTrait
{
    /**
     * @var null|DateTime
     */
    private ?DateTime $archivedAt = null;

    /**
     * @inheritDoc
     */
    public function archivedAt(): ?DateTime
    {
        return $this->archivedAt;
    }

    /**
     * @inheritDoc
     */
    public function archive(): bool
    {
        return $this->setArchivedAt(new DateTime);
    }

    /**
     * @inheritDoc
     */
    public function restore(): bool
    {
        return $this->setArchivedAt(null);
    }

    /**
     * @inheritDoc
     */
    public function isArchived(): bool
    {
        return $this->archivedAt !== null;
    }

    /**
     * @param null|DateTime $archivedAt
     *
     * @return bool
     */
    private function setArchivedAt(?DateTime $archivedAt): bool
    {
        $this->archivedAt = $archivedAt;

        return true;
    }
}
