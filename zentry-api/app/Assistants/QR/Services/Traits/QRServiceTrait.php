<?php

namespace App\Assistants\QR\Services\Traits;

use App\Assistants\QR\Services\QRServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait QRServiceTrait
 *
 * @package App\Components\Users\Services\QR\Traits
 */
trait QRServiceTrait
{
    /**
     * @var QRServiceContract | null
     */
    private ?QRServiceContract $QRService__ = null;

    /**
     * @return QRServiceContract
     * @throws BindingResolutionException
     */
    private function QRService__(): QRServiceContract
    {
        if (!$this->QRService__ instanceof QRServiceContract) {
            $this->QRService__ = app()->make(QRServiceContract::class);
        }

        return $this->QRService__;
    }
}
