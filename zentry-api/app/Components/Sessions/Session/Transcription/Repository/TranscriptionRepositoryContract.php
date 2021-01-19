<?php

namespace App\Components\Sessions\Session\Transcription\Repository;

use App\Components\Sessions\Session\Transcription\Mutators\DTO\Mutator;
use App\Components\Sessions\Session\Transcription\TranscriptionContract;
use App\Components\Sessions\Session\Transcription\TranscriptionEntity;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use LogicException;
use UnexpectedValueException;

/**
 * Interface TranscriptionRepositoryContract
 *
 * @package App\Components\Sessions\Session\Transcription\Repository
 */
interface TranscriptionRepositoryContract
{
    public const CLASS_NAME = TranscriptionEntity::class;

    public const ALIAS = Mutator::TYPE;

    /**
     * @param Identity $identity
     *
     * @return TranscriptionContract
     * @throws NotFoundException
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     * @throws MappingException
     * @throws LogicException
     * @throws LockException
     */
    public function byIdentity(Identity $identity): TranscriptionContract;

    /**
     * @return TranscriptionContract|null
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    public function getOne(): ?TranscriptionContract;

    /**
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * @param TranscriptionContract $entity
     *
     * @return TranscriptionContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function persist(TranscriptionContract $entity): TranscriptionContract;

    /**
     * @param TranscriptionContract $entity
     *
     * @return bool
     * @throws BindingResolutionException|InvalidArgumentException
     */
    public function destroy(TranscriptionContract $entity): bool;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return TranscriptionRepositoryContract
     * @throws BindingResolutionException|UnexpectedValueException
     */
    public function filterByUsersIds(array $ids, bool $contains = true): TranscriptionRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return TranscriptionRepositoryContract
     * @throws BindingResolutionException|UnexpectedValueException
     */
    public function filterBySessionIds(array $ids, bool $contains = true): TranscriptionRepositoryContract;

    /**
     * @param array $ids
     * @param bool  $contains
     *
     * @return TranscriptionRepositoryContract
     * @throws BindingResolutionException|UnexpectedValueException
     */
    public function filterByPoisIds(array $ids, bool $contains = true): TranscriptionRepositoryContract;
}
