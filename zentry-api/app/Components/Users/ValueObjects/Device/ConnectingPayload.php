<?php

namespace App\Components\Users\ValueObjects\Device;

use App\Assistants\Transformers\Contracts\PresenterContract;
use App\Assistants\Transformers\JsonApi\Traits\PresenterTrait;
use App\Convention\Generators\Identity\IdentityGenerator;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class ConnectingPayload
 *
 * @package App\Components\Users\ValueObjects\Device
 */
final class ConnectingPayload implements PresenterContract
{
    use PresenterTrait;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $model;

    /**
     * @var string
     */
    private string $reference;

    /**
     * @var string
     */
    protected string $_type = 'users_devices_connecting_payload';

    /**
     * @param string $type
     * @param string $model
     * @param string $reference
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $type, string $model, string $reference)
    {
        $this->id = IdentityGenerator::next();
        $this->setType($type);
        $this->setModel($model);
        $this->setReference($reference);
    }

    /**
     * @return string
     */
    public function deviceType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return ConnectingPayload
     * @throws InvalidArgumentException
     */
    private function setType(string $type): ConnectingPayload
    {
        if (strEmpty($type)) {
            throw new InvalidArgumentException("Type can't be empty");
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function model(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     *
     * @return ConnectingPayload
     * @throws InvalidArgumentException
     */
    private function setModel(string $model): ConnectingPayload
    {
        if (strEmpty($model)) {
            throw new InvalidArgumentException("Model can't be empty");
        }

        $this->model = $model;

        return $this;
    }

    /**
     * @return string
     */
    public function reference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return ConnectingPayload
     * @throws InvalidArgumentException
     */
    private function setReference(string $reference): ConnectingPayload
    {
        if (strEmpty($reference)) {
            throw new InvalidArgumentException("Reference can't be empty");
        }

        $this->reference = $reference;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function attributes(): Collection
    {
        return collect(
            [
                'type' => $this->deviceType(),
                'model' => $this->model(),
                'reference' => $this->reference(),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'type' => $this->deviceType(),
            'model' => $this->model(),
            'reference' => $this->reference(),
        ];
    }
}
