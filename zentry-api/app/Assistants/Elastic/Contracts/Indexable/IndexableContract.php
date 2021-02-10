<?php

namespace App\Assistants\Elastic\Contracts\Indexable;

use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\ValueObjects\Document;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Queueable;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;

/**
 * Interface IndexableContract
 *
 * @package App\Assistants\Elastic\Contracts
 */
interface IndexableContract extends SetupableContract
{
    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function asIdentity(): Identity;

    /**
     * @param Index $index
     *
     * @return Document
     * @throws IndexNotSupported|NotFoundException|PropertyNotInit|InvalidArgumentException|Exception
     */
    public function asDocument(Index $index): Document;

    /**
     * @param Index $index
     *
     * @return Queueable
     * @throws BindingResolutionException
     */
    public function forQueue(Index $index): Queueable;

    /**
     * @param bool $withJob
     *
     * @return void
     * @throws BindingResolutionException|NotImplementedException
     */
    public function stateChanged(bool $withJob = true): void;

    /**
     * @param bool $withJob
     *
     * @return void
     * @throws BindingResolutionException|NotImplementedException
     */
    public function stateDeletion(bool $withJob = true): void;
}
