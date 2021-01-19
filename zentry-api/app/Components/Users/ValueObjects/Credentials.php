<?php

namespace App\Components\Users\ValueObjects;

use App\Components\Users\ValueObjects\Device\ConnectingPayload;
use App\Convention\Contracts\Arrayable;

/**
 * Class Credentials
 *
 * @package App\Components\Users\ValueObjects
 */
final class Credentials implements Arrayable
{
    /**
     * @var Email
     */
    private Email $email;

    /**
     * @var HashedPassword
     */
    private HashedPassword $password;

    /**
     * @var HashedPassword|null
     */
    private ?HashedPassword $passwordRepeat;

    /**
     * @var bool
     */
    private bool $remember;

    /**
     * @var ConnectingPayload|null
     */
    private ?ConnectingPayload $devicePayload;

    /**
     * Credentials constructor.
     *
     * @param Email                  $email
     * @param HashedPassword         $password
     * @param HashedPassword|null    $passwordRepeat
     * @param bool                   $remember
     * @param ConnectingPayload|null $devicePayload
     */
    public function __construct(
        Email $email,
        HashedPassword $password,
        HashedPassword $passwordRepeat = null,
        bool $remember = false,
        ?ConnectingPayload $devicePayload = null
    ) {
        $this->email = $email;
        $this->password = $password;
        $this->passwordRepeat = $passwordRepeat;
        $this->remember = $remember;
        $this->devicePayload = $devicePayload;
    }

    /**
     * @return Email
     */
    public function email(): Email
    {
        return $this->email;
    }

    /**
     * @return HashedPassword
     */
    public function password(): HashedPassword
    {
        return $this->password;
    }

    /**
     * @return HashedPassword|null
     */
    public function passwordRepeat(): ?HashedPassword
    {
        return $this->passwordRepeat;
    }

    /**
     * @return bool
     */
    public function remember(): bool
    {
        return $this->remember;
    }

    /**
     * @return ConnectingPayload|null
     */
    public function devicePayload(): ?ConnectingPayload
    {
        return $this->devicePayload;
    }

    /**
     * @param Credentials $credentials
     *
     * @return bool
     */
    public function equals(Credentials $credentials): bool
    {
        return $this->email()->equals($credentials->email()) && $this->password()->equals(
                $credentials->password()
            );
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $data = [
            'email' => $this->email()->toString(),
            'password' => $this->password()->raw(),
        ];
        $passwordRepeat = $this->passwordRepeat();

        if ($passwordRepeat instanceof HashedPassword) {
            $data['password_repeat'] = $passwordRepeat->raw();
        }

        return $data;
    }
}
