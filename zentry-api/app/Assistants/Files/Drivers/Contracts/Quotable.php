<?php

namespace App\Assistants\Files\Drivers\Contracts;

use App\Assistants\Files\Drivers\ValueObjects\Quota;

/**
 * Interface Quotable
 *
 * @package App\Convention\Contracts\File\Driver
 */
interface Quotable
{
    /**
     * @param string|null $path
     *
     * @return Quota
     */
    public function quota(string $path = null): Quota;
}
