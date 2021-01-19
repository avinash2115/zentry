<?php

namespace App\Assistants\Transformers\Contracts;

use App\Convention\Contracts\Arrayable;
use App\Assistants\Transformers\Contracts\Presenter\AttributesContract;

/**
 * Interface PresenterContract
 *
 * @package App\Assistants\Transformers\Contracts
 */
interface PresenterContract extends AttributesContract, Arrayable
{
    /**
     * @return array
     */
    public function present(): array;

    /**
     * @return string
     */
    public function id(): string;

    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return array
     */
    public function meta(): array;

    /**
     * @return bool
     */
    public function linksEnabled(): bool;

    /**
     * @return void
     */
    public function enableLinks(): void;

    /**
     * @return void
     */
    public function disableLinks(): void;

    /**
     * @param array $meta
     */
    public function fillMeta(array $meta): void;
}
