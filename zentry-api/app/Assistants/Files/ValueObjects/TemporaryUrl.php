<?php

namespace App\Assistants\Files\ValueObjects;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Support\Collection;

/**
 * Class TemporaryUrl
 *
 * @package App\Components\Files\ValueObjects
 */
final class TemporaryUrl implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $url;

    /**
     * @var string
     */
    private string $_type = 'files_temporary_urls';

    /**
     * @param string        $name
     * @param string        $url
     * @param Identity|null $identity
     */
    public function __construct(string $name, string $url, Identity $identity = null)
    {
        $this->id = $identity instanceof Identity ? $identity->toString() : IdentityGenerator::next()->toString();
        $this->setName($name);
        $this->setUrl($url);
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    private function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    private function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'url' => $this->url(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect($this->toArray());
    }
}
