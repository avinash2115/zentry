<?php

namespace App\Components\Sessions\Services\Transcription\Traits;

use App\Components\Sessions\Services\Transcription\TranscriptionServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait TranscriptionServiceTrait
 *
 * @package App\Components\Sessions\Services\Transcription\Traits
 */
trait TranscriptionServiceTrait
{
    /**
     * @var TranscriptionServiceContract | null
     */
    private ?TranscriptionServiceContract $transcriptionService__ = null;

    /**
     * @return TranscriptionServiceContract
     * @throws BindingResolutionException
     */
    private function transcriptionService__(): TranscriptionServiceContract
    {
        if (!$this->transcriptionService__ instanceof TranscriptionServiceContract) {
            $this->transcriptionService__ = app()->make(TranscriptionServiceContract::class);
        }

        return $this->transcriptionService__;
    }
}
