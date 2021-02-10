<?php

namespace App\Components\Users\ValueObjects\Storage;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Collection;

/**
 * Class Driver
 *
 * @package App\Components\Users\ValueObjects\Storage
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
    public string $_type = 'users_storages_drivers';

    /**
     * @param string $type
     * @param string $title
     * @param array  $config
     */
    public function __construct(string $type, string $title, array $config)
    {
        $this->id = IdentityGenerator::next()->toString();
        $this->type = $type;

        if ($this->type === StorageReadonlyContract::DRIVER_DEFAULT) {
            $this->title = str_replace(
                StorageReadonlyContract::LABEL_PLACEHOLDER_APP_NAME,
                env('APP_NAME', 'Zentry'),
                $title
            );
        } else {
            $this->title = $title;
        }

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
}
