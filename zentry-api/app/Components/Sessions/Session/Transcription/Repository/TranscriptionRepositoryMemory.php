<?php

namespace App\Components\Sessions\Session\Transcription\Repository;

use App\Components\Sessions\Session\Transcription\TranscriptionContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Repositories\Abstracts\AbstractMemoryRepository;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Support\Collection;

/**
 * Class TranscriptionRepositoryMemory
 *
 * @package App\Components\Sessions\Session\Transcription\Repository
 */
class TranscriptionRepositoryMemory extends AbstractMemoryRepository implements TranscriptionRepositoryContract
{
    /**
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
        $entity = $this->collector->get($identity->toString());

        if (!$entity instanceof TranscriptionContract) {
            throw new NotFoundException('Not Found Exception');
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getOne(): ?TranscriptionContract
    {
        return $this->collector->first();
    }

    /**
     * @inheritDoc
     */
    public function persist(TranscriptionContract $entity): TranscriptionContract
    {
        $this->collector->put($entity->identity()->toString(), $entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function destroy(TranscriptionContract $entity): bool
    {
        $this->collector->forget($entity->identity()->toString());

        return true;
    }

    /**
     * @inheritdoc
     */
    public function filterByUsersIds(array $ids, bool $contains = true): TranscriptionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterBySessionIds(array $ids, bool $contains = true): TranscriptionRepositoryContract
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByPoisIds(array $ids, bool $contains = true): TranscriptionRepositoryContract
    {
        return $this;
    }
}
