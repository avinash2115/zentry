<?php

namespace App\Components\CRM\ValueObjects;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Collection;

/**
 * Class Driver
 *
 * @package App\Components\CRM\ValueObjects
 */
final class Driver implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var array
     */
    private array $config;

    /**
     * @var string
     */
    public string $_type = 'users_crms_drivers';

    /**
     * @param string $type
     * @param string $title
     * @param array  $config
     */
    public function __construct(string $type, string $title, array $config)
    {
        $this->id = IdentityGenerator::next()->toString();
        $this->type = $type;

        $this->title = $title;

        $this->config = $config;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'config' => $this->config,
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect($this->toArray());
    }

    /**
     * @return array
     */
    public function config(): array
    {
        return $this->config;
    }
}
