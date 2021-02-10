<?php

namespace App\Convention\Entities\Traits;

use App\Convention\ValueObjects\Identity\Identity;

/**
 * Trait IdentifiableTrait
 *
 * @package App\Convention\Entities\Traits
 */
trait IdentifiableTrait
{
    /**
     * @var Identity
     */
    protected Identity $identity;

    /**
     * @inheritDoc
     */
    public function identity(): Identity
    {
        return $this->identity;
    }

    /**
     * @inheritDoc
     */
    private function setIdentity(Identity $identity): void
    {
        $this->identity = $identity;
    }
}
