<?php

namespace App\Assistants\QR\Services;

use App\Assistants\QR\ValueObjects\Payload;
use QrCode;
use RuntimeException;

/**
 * Class QRService
 *
 * @package App\Assistants\QR\Services
 */
class QRService implements QRServiceContract
{
    /**
     * @inheritDoc
     */
    public function render(Payload $payload): string
    {
        $result = QrCode::generate($payload->text());

        if (!is_string($result)) {
            throw new RuntimeException("QR code with {$payload->text()} can't be generated");
        }

        return $result;
    }
}