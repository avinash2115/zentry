<?php

namespace App\Components\Share\Shared;

use App\Components\Share\ValueObjects\Payload;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class SharedEntity
 *
 * @package App\Components\Share\Shared
 */
class SharedEntity implements SharedContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var Payload
     */
    private Payload $payload;

    /**
     * @param Identity $identity
     * @param string   $type
     * @param Payload  $payload
     *
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        string $type,
        Payload $payload
    ) {
        $this->setIdentity($identity);
        $this->setType($type)
            ->setPayload($payload);

        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @param Payload $payload
     *
     * @return SharedEntity
     */
    private function setPayload(Payload $payload): SharedEntity
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function payload(): Payload
    {
        return $this->payload;
    }

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return SharedEntity
     * @throws InvalidArgumentException
     */
    private function setType(string $type): SharedEntity
    {
        if (strEmpty($type)) {
            throw new InvalidArgumentException("Type can't be empty");
        }

        $this->type = $type;

        return $this;
    }
}
