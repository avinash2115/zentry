<?php

namespace App\Assistants\Elastic\ValueObjects;

use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Class Document
 *
 * @package App\Assistants\Elastic\ValueObjects
 */
final class Document implements IdentifiableContract
{
    use IdentifiableTrait;

    public const DOCUMENT_TYPE_KEY = '_document_type';

    /**
     * @var Index
     */
    private Index $index;

    /**
     * @var Type
     */
    private Type $type;

    /**
     * @var Body
     */
    private Body $body;

    /**
     * @param Index    $index
     * @param Type     $type
     * @param Identity $identity
     * @param Body     $body
     */
    public function __construct(Index $index, Type $type, Identity $identity, Body $body)
    {
        $this->setIndex($index);
        $this->setType($type);
        $this->setIdentity($identity);
        $this->setBody($body);
    }

    /**
     * @return Index
     */
    public function index(): Index
    {
        return $this->index;
    }

    /**
     * @param Index $index
     *
     * @return Document
     */
    private function setIndex(Index $index): Document
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return Type
     */
    public function type(): Type
    {
        return $this->type;
    }

    /**
     * @param Type $type
     *
     * @return Document
     */
    private function setType(Type $type): Document
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Body
     */
    public function body(): Body
    {
        return $this->body;
    }

    /**
     * @param Body $body
     *
     * @return Document
     */
    private function setBody(Body $body): Document
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'id' => $this->identity()->toString(),
            'index' => $this->index()->elasticIndex(),
            'type' => $this->index()->type(),
            'body' => [
                'doc' => array_merge([self::DOCUMENT_TYPE_KEY => $this->type()->type()], $this->body()->body()),
                'doc_as_upsert' => true,
            ],
        ];
    }
}
