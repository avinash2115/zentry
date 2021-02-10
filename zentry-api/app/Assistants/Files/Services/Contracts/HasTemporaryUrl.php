<?php

namespace App\Assistants\Files\Services\Contracts;

use App\Assistants\Files\ValueObjects\TemporaryUrl;
use Illuminate\Contracts\Container\BindingResolutionException;
use RuntimeException;

/**
 * Interface HasTemporaryUrl
 */
interface HasTemporaryUrl
{
    /**
     * @return TemporaryUrl
     * @throws BindingResolutionException|RuntimeException
     */
    public function temporaryUrl(): TemporaryUrl;
}