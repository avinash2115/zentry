<?php

namespace App\Components\Sessions\Session\Stream;

use App\Assistants\Files\ValueObjects\File;
use App\Components\Sessions\Session\SessionContract;
use App\Convention\Entities\Traits\HasCreatedAtTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class StreamEntity
 *
 * @package App\Components\Sessions\Session\Stream
 */
class StreamEntity implements StreamContract
{
    use IdentifiableTrait;
    use HasCreatedAtTrait;

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
     * @var int
     */
    private int $convertProgress;

    /**
     * @var SessionContract
     */
    private SessionContract $session;

    /**
     * @param Identity        $identity
     * @param SessionContract $session
     * @param string          $type
     * @param File            $file
     *
     * @throws InvalidArgumentException|RuntimeException|Exception
     */
    public function __construct(
        Identity $identity,
        SessionContract $session,
        string $type,
        File $file
    ) {
        $this->setIdentity($identity);

        $this->setType($type)->setSession($session)->setName($file->name())->setUrl($file->url());

        switch ($this->type()) {
            case self::AUDIO_TYPE:
                $this->convertProgressAdvance(100);
            break;
            default:
                $this->convertProgressAdvance(0);
            break;
        }

        $this->setCreatedAt();
    }

    /**
     * @param SessionContract $session
     *
     * @return StreamEntity
     */
    private function setSession(SessionContract $session): StreamEntity
    {
        $this->session = $session;

        return $this;
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
     * @param string $type
     *
     * @return StreamEntity
     * @throws InvalidArgumentException
     */
    private function setType(string $type): StreamEntity
    {
        if (!in_array($type, self::AVAILABLE_TYPES, true)) {
            throw new InvalidArgumentException("Type {$type} is not allowed");
        }

        $this->type = $type;

        return $this;
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
    public function changeName(string $name): StreamContract
    {
        return $this->setName($name);
    }

    /**
     * @param string $name
     *
     * @return StreamEntity
     * @throws InvalidArgumentException
     */
    private function setName(string $name): StreamEntity
    {
        if (strEmpty($name)) {
            throw new InvalidArgumentException("Name can't be empty");
        }

        $this->name = $name;

        return $this;
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
    public function changeUrl(string $url): StreamContract
    {
        return $this->setUrl($url);
    }

    /**
     * @param string $url
     *
     * @return StreamEntity
     */
    private function setUrl(string $url): StreamEntity
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function convertProgress(): int
    {
        return $this->convertProgress;
    }

    /**
     * @inheritDoc
     */
    public function isConverted(): bool
    {
        return $this->convertProgress === 100;
    }

    /**
     * @inheritDoc
     */
    public function convertProgressAdvance(int $value): StreamContract
    {
        if ($value < 0) {
            $value = 0;
        }

        $this->convertProgress = $value;

        return $this;
    }
}
