<?php
namespace App\Convention\Entities\Traits;

use DateTime;
use Exception;

/**
 * Trait HasCreatedAtTrait
 *
 * @package App\Convention\Entities\Traits
 */
trait HasCreatedAtTrait
{
    /**
     * @var DateTime
     */
    private DateTime $createdAt;

    /**
     * @inheritDoc
     */
    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @throws Exception
     */
    protected function setCreatedAt(): void
    {
        $this->createdAt = new DateTime();
    }
}
