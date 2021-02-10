<?php

namespace App\Components\Sessions\Session\Transcription\Repository;

use App\Components\Sessions\Session\Transcription\TranscriptionContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractODMRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use UnexpectedValueException;

/**
 * Class TranscriptionRepositoryODM
 *
 * @package App\Components\Sessions\Session\Transcription\Repository
 */
class TranscriptionRepositoryODM extends AbstractODMRepository implements TranscriptionRepositoryContract
{
    /**
     * TranscriptionRepositoryODM constructor.
     *
     * @throws BindingResolutionException
     * @throws UnexpectedValueException
     */
    public function __construct()
    {
        parent::__construct(self::CLASS_NAME, self::ALIAS);
    }

    /**
     * @inheritDoc
     */
    public function byIdentity(Identity $identity): TranscriptionContract
    {
        $entity = $this->documentRepository()->find($identity);

        if (!$entity instanceof TranscriptionContract) {
            throw new NotFoundException('Not Found Exception');
        }

        $this->refreshBuilder();

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?TranscriptionContract
    {
        /**
         * @var TranscriptionContract | null $result
         */
        $result = $this->builder()->getQuery()->getSingleResult();

        $this->refreshBuilder();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function persist(TranscriptionContract $entity): TranscriptionContract
    {
        $this->manager()->persist($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(TranscriptionContract $entity): bool
    {
        $this->manager()->remove($entity);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): TranscriptionRepositoryContract
    {
        if ($contains) {
            $this->builder()->field('userIdentity')->in($ids);
        } else {
            $this->builder()->field('userIdentity')->notIn($ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySessionIds(array $ids, bool $contains = true): TranscriptionRepositoryContract
    {
        if ($contains) {
            $this->builder()->field('sessionIdentity')->in($ids);
        } else {
            $this->builder()->field('sessionIdentity')->notIn($ids);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByPoisIds(array $ids, bool $contains = true): TranscriptionRepositoryContract
    {
        if ($contains) {
            $this->builder()->field('poiIdentity')->in($ids);
        } else {
            $this->builder()->field('poiIdentity')->notIn($ids);
        }

        return $this;
    }
}
