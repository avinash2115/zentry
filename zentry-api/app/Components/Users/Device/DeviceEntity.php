<?php

namespace App\Components\Users\Device;

use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class DeviceEntity
 *
 * @package App\Components\Users\Device
 */
class DeviceEntity implements DeviceContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

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
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @param Identity             $identity
     * @param UserReadonlyContract $user
     * @param ConnectingPayload    $payload
     *
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        UserReadonlyContract $user,
        ConnectingPayload $payload
    ) {
        $this->setIdentity($identity);
        $this->setType($payload->deviceType())
            ->setModel($payload->model())
            ->setReference($payload->reference());

        $this->setCreatedAt();
        $this->setUpdatedAt();

        $this->setUser($user);
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return DeviceEntity
     */
    private function setUser(UserReadonlyContract $user): DeviceEntity
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function user(): UserReadonlyContract
    {
        return $this->user;
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
     * @return DeviceEntity
     */
    private function setType(string $type): DeviceEntity
    {
        if (strEmpty($type)) {
            throw new InvalidArgumentException("Type can't be empty");
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return DeviceEntity
     */
    private function setReference(string $reference): DeviceEntity
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
    public function model(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     *
     * @return DeviceEntity
     */
    private function setModel(string $model): DeviceEntity
    {
        if (strEmpty($model)) {
            throw new InvalidArgumentException("Model can't be empty");
        }

        $this->model = $model;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function transfer(UserReadonlyContract $user): DeviceContract
    {
        $this->user = $user;

        return $this;
    }
}
