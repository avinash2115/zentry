<?php

namespace App\Assistants\Transformers\JsonApi;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Contracts\JsonAPIPresentableContract;
use Illuminate\Support\Collection;

/**
 * Class Body
 *
 * @package App\Assistants\Transformers\JsonApi
 */
class Body implements JsonAPIPresentableContract
{
    /**
     * @var string
     */
    public string $id;

    /**
     * @var string
     */
    public string $type;

    /**
     * @var array
     */
    public array $meta = [];

    /**
     * @param PresenterContract $body
     */
    public function __construct(PresenterContract $body)
    {
        $this->setId($body->id())->setType($body->type())->setMeta($body->meta());
    }

    /**
     * @param string $id
     *
     * @return Body
     */
    private function setId(string $id): Body
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return Body
     */
    private function setType(string $type): Body
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param array $meta
     *
     * @return Body
     */
    private function setMeta(array $meta): Body
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return Collection
     */
    public function present(): Collection
    {
        $collection = collect(
            [
                'type' => $this->type,
                'id' => $this->id,
            ]
        );

        if ($this->meta) {
            $collection->put('meta', $this->meta);
        }

        return $collection;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return strEmpty($this->id()) && strEmpty($this->type());
    }
}
