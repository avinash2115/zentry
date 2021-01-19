<?php

namespace App\Components\Sessions\Services\Transcription;

use App\Components\Sessions\Session\Transcription\TranscriptionDTO;
use App\Components\Sessions\Session\Transcription\TranscriptionReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use LogicException;
use UnexpectedValueException;

/**
 * Interface TranscriptionServiceContract
 *
 * @package App\Components\Sessions\Services\Transcription
 */
interface TranscriptionServiceContract extends FilterableContract
{
    /**
     * @param string $id
     *
     * @return TranscriptionServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|PropertyNotInit
     * @throws UnexpectedValueException|LockException|MappingException|LogicException
     */
    public function workWith(string $id): TranscriptionServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return TranscriptionReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): TranscriptionReadonlyContract;

    /**
     * @return TranscriptionDTO
     * @throws PropertyNotInit|BindingResolutionException
     */
    public function dto(): TranscriptionDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     * @throws NotFoundException
     */
    public function listRO(): Collection;

    /**
     * @param array $data
     *
     * @return TranscriptionServiceContract
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function create(array $data): TranscriptionServiceContract;

    /**
     * @return TranscriptionServiceContract
     * @throws PropertyNotInit|BindingResolutionException|InvalidArgumentException
     */
    public function remove(): TranscriptionServiceContract;
}
