<?php

namespace App\Components\Sessions\ValueObjects\Stream\Convert;

use App\Assistants\Files\ValueObjects\File;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Convention\Entities\Traits\HasCreatedAtTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Generators\Identity\IdentityGenerator;
use Exception;

/**
 * Class Stream
 *
 * @package App\Components\Sessions\ValueObjects\Stream\Convert
 */
final class Stream implements StreamReadonlyContract
{
    use HasCreatedAtTrait;
    use IdentifiableTrait;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $url;

    /**
     * Stream constructor.
     *
     * @param string $type
     * @param File   $file
     *
     * @throws Exception
     */
    public function __construct(string $type, File $file)
    {
        $this->type = $type;
        $this->name = $file->name();
        $this->url = $file->url();

        $this->setIdentity(IdentityGenerator::next());
        $this->setCreatedAt();
    }

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function isType(string $type): bool
    {
        return $this->type() === $type;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function convertProgress(): int
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }

    /**
     * @inheritDoc
     */
    public function isConverted(): bool
    {
        throw new NotImplementedException(__METHOD__, __CLASS__);
    }
}
