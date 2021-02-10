<?php

namespace App\Components\Sessions\Services\Transcription\Contracts;

use App\Components\Sessions\ValueObjects\Transcription\Transcript;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;

/**
 * Interface HasTranscription
 *
 * @package App\Components\Sessions\Services\Transcription\Contracts
 */
interface HasTranscription
{
    /**
     * @return InjectedDTO
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function injectedDTO(): InjectedDTO;

    /**
     * @return Collection
     * @throws PropertyNotInit
     */
    public function injectedList(): Collection;

    /**
     * @return Transcript|null
     * @throws RuntimeException
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    public function transcript(): ?Transcript;

    /**
     * @return Collection
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    public function words(): Collection;

    /**
     * @return Collection
     */
    public function wordsSimplified(): Collection;
}
