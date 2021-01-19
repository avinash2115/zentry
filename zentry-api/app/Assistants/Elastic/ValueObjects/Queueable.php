<?php

namespace App\Assistants\Elastic\ValueObjects;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\ValueObjects\Identity\Identity;
use RuntimeException;

/**
 * Class Queueable
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class Queueable implements IndexableContract, IdentifiableContract
{
    use IdentifiableTrait;

    /**
     * @var Type
     */
    private Type $type;

    /**
     * @var Document | null
     */
    private ?Document $document;

    /**
     * @var Mappings | null
     */
    private ?Mappings $mappings;

    /**
     * @param Identity      $identity
     * @param Type          $type
     * @param Document|null $document
     * @param Mappings|null $mappings
     */
    public function __construct(Identity $identity, Type $type, ?Document $document = null, ?Mappings $mappings = null)
    {
        $this->setIdentity($identity);
        $this->type = $type;
        $this->document = $document;
        $this->mappings = $mappings;
    }

    /**
     * @inheritDoc
     */
    public function asIdentity(): Identity
    {
        return $this->identity();
    }

    /**
     * @inheritdoc
     */
    public function asType(): Type
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function asDocument(Index $index): Document
    {
        if (!$this->document instanceof Document) {
            throw new RuntimeException("Queueable without document can be used only for removal");
        }

        return $this->document;
    }

    /**
     * @inheritdoc
     */
    public function asMappings(Index $index): Mappings
    {
        if (!$this->mappings instanceof Mappings) {
            throw new RuntimeException("Queueable without mapping can be used only for removal");
        }

        return $this->mappings;
    }

    /**
     * @inheritdoc
     */
    public function forQueue(Index $index): Queueable
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function stateChanged(bool $withJob = true): void
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritdoc
     */
    public function stateDeletion(bool $withJob = true): void
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }
}
