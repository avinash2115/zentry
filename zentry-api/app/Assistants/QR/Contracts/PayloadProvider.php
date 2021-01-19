<?php

namespace App\Assistants\QR\Contracts;

use App\Assistants\QR\ValueObjects\Payload;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;

/**
 * Interface PayloadProvider
 *
 * @package App\Assistants\QR\Contracts
 */
interface PayloadProvider
{
    /**
     * @return Payload
     * @throws PropertyNotInit
     */
    public function asQRPayload(): Payload;
}