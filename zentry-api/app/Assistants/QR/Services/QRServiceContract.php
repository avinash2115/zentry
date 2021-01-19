<?php

namespace App\Assistants\QR\Services;

use App\Assistants\QR\ValueObjects\Payload;

/**
 * Interface QRServiceContract
 *
 * @package App\Assistants\QR\Services
 */
interface QRServiceContract
{
    /**
     * @param Payload $payload
     *
     * @return string
     */
    public function render(Payload $payload): string;
}