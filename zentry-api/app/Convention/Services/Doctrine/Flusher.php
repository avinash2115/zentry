<?php

namespace App\Convention\Services\Doctrine;

use App\Assistants\Events\EventRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Container\BindingResolutionException;
use RuntimeException;

/**
 * Class Flusher
 *
 * @package App\Convention\Services\Doctrine
 */
class Flusher implements FlusherContract
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    /**
     * @var DocumentManager
     */
    private DocumentManager $documentManager;

    /**
     * @param EntityManagerInterface $manager
     * @param DocumentManager        $documentManager
     */
    public function __construct(
        EntityManagerInterface $manager,
        DocumentManager $documentManager

    ) {
        $this->manager = $manager;
        $this->documentManager = $documentManager;
    }

    /**
     * @inheritdoc
     */
    public function open(): void
    {
        if (!$this->isOpened()) {
            $this->manager->getConnection()->beginTransaction();
        }
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
        $this->manager->flush();
        $this->documentManager->flush();
    }

    /**
     * @inheritDoc
     */
    public function commit(bool $fake = false): void
    {
        $this->flush();

        if (!$fake && $this->isOpened()) {
            $this->manager->commit();
        }

        $this->fireEvents();
    }

    /**
     * @inheritdoc
     */
    public function rollback(bool $fireBroadcast = false): void
    {
        while ($this->isOpened()) {
            $this->manager->rollback();
        }
    }

    /**
     * @inheritdoc
     */
    public function clear(string $objectName = null): void
    {
        $this->clearManager($objectName);
        $this->clearDocumentManager($objectName);
    }

    /**
     * @param string|null $objectName
     *
     * @throws RuntimeException
     */
    private function clearManager(string $objectName = null): void
    {
        $unitOfWork = $this->manager->getUnitOfWork();

        if (count($unitOfWork->getScheduledEntityInsertions())) {
            throw new RuntimeException('[ORM] INSERT:: Call clear method with filled unit of work.');
        }

        if (count($unitOfWork->getScheduledEntityUpdates())) {
            throw new RuntimeException('[ORM] UPDATE:: Call clear method with filled unit of work.');
        }

        if (count($unitOfWork->getScheduledEntityDeletions())) {
            throw new RuntimeException('[ORM] DELETE:: Call clear method with filled unit of work.');
        }

        $this->manager->clear($objectName);
    }

    /**
     * @param string|null $objectName
     *
     * @throws RuntimeException
     */
    private function clearDocumentManager(string $objectName = null): void
    {
        $unitOfWork = $this->documentManager->getUnitOfWork();

        if (count($unitOfWork->getScheduledDocumentInsertions())) {
            throw new RuntimeException('[ODM] INSERT:: Call clear method with filled unit of work.');
        }

        if (count($unitOfWork->getScheduledDocumentUpdates())) {
            throw new RuntimeException('[ODM] UPDATE:: Call clear method with filled unit of work.');
        }

        if (count($unitOfWork->getScheduledDocumentDeletions())) {
            throw new RuntimeException('[ODM] DELETE:: Call clear method with filled unit of work.');
        }

        $this->documentManager->clear($objectName);
    }

    /**
     * @return bool
     */
    private function isOpened(): bool
    {
        return $this->manager->getConnection()->getTransactionNestingLevel() !== 0;
    }

    /**
     * @throws BindingResolutionException
     */
    private function fireEvents(): void
    {
        $registry = app()->make(EventRegistry::class);

        $registry->list()->each(
            static function (object $event) {
                event($event);
            }
        );
        $registry->broadcastList()->each(
            static function (ShouldBroadcast $event) {
                event($event);
            }
        );

        $registry->flushEvents();
    }
}
