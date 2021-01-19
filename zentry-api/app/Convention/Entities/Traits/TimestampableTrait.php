<?php
namespace App\Convention\Entities\Traits;

use DateTime;
use Exception;

/**
 * Trait TimestampableTrait
 *
 * @package App\Convention\Entities\Traits
 */
trait TimestampableTrait
{
    use HasCreatedAtTrait;

    /**
     * @var DateTime
     */
    private DateTime $updatedAt;

    /**
     * @inheritDoc
     */
    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @throws Exception
     */
    protected function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTime();
    }
}
