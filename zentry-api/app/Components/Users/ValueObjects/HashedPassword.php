<?php

namespace App\Components\Users\ValueObjects;

use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

/**
 * Class HashedPassword
 *
 * @package App\Components\Users\ValueObjects
 */
final class HashedPassword
{
    public const MIN_LENGTH = 8;

    /**
     * @var string
     */
    private string $password;

    /**
     * @param string $password
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $password)
    {
        $this->setPassword($password);
    }

    /**
     * @param string $password
     *
     * @return HashedPassword
     * @throws InvalidArgumentException
     */
    private function setPassword(string $password): HashedPassword
    {
        if (strlen(trim($password)) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('Password must be at least 8 characters long.');
        }

        $this->password = $password;

        return $this;
    }

    /**
     * @param HashedPassword $credentials
     *
     * @return bool
     */
    public function equals(HashedPassword $credentials): bool
    {
        return $this->raw() === $credentials->raw();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return Hash::make($this->raw());
    }

    /**
     * @return string
     */
    public function raw(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
