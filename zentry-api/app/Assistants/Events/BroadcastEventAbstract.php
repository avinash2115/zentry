<?php

namespace App\Assistants\Events;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\Presenter;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Class BroadcastEventAbstract
 *
 * @package App\Assistants\Events
 */
abstract class BroadcastEventAbstract implements ShouldBroadcastNow
{
    public const USER_CHANNEL_BASE = 'users-' . self::USER_CHANNEL_PARAMETER;

    public const USER_CHANNEL_PARAMETER = '{userId}';

    /**
     * @var array | null
     */
    public ?array $dto = null;

    /**
     * @var Presenter|null
     */
    private ?Presenter $presenter = null;

    /**
     * @param PresenterContract $presenter
     *
     * @return BroadcastEventAbstract
     * @throws BindingResolutionException
     */
    public function withDTO(PresenterContract $presenter): BroadcastEventAbstract
    {
        $this->dto = $this->_presenter()->present($presenter);

        return $this;
    }

    /**
     * @param Collection $collection
     *
     * @return BroadcastEventAbstract
     * @throws BindingResolutionException
     */
    public function withDTOCollection(Collection $collection): BroadcastEventAbstract
    {
        $this->dto = $this->_presenter()->present($collection);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function broadcastOn()
    {
        if (!is_array($this->dto)) {
            throw new InvalidArgumentException('DTO is missed. Please call withDTO or withDTOCollection first.');
        }

        return $this->getBroadcastChannels();
    }

    /**
     * @return string
     * @throws InvalidArgumentException
     */
    public function broadcastAs(): string
    {
        if (!is_array($this->dto)) {
            throw new InvalidArgumentException('DTO is missed. Please call withDTO or withDTOCollection first.');
        }

        return $this->defaultBroadcastAs();
    }

    /**
     * @return array
     */
    abstract public function getBroadcastChannels(): array;

    /**
     * Generate message name.
     * {component}.{action} EX field.added
     *
     * @return string
     */
    protected function defaultBroadcastAs(): string
    {
        return Str::snake(class_basename(static::class), '.');
    }

    /**
     * @return Presenter
     * @throws BindingResolutionException
     */
    private function _presenter(): Presenter
    {
        if (!$this->presenter instanceof Presenter) {
            $this->presenter = app()->make(Presenter::class);
        }

        return $this->presenter;
    }
}
