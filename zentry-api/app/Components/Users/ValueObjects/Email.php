<?php

namespace App\Components\Users\ValueObjects;

use InvalidArgumentException;

/**
 * Class Email
 *
 * @package App\Components\Users\ValueObjects
 */
final class Email
{
    /**
     * @var string
     */
    private string $email;

    /**
     * @param string $email
     */
    public function __construct(string $email)
    {
        $this->setEmail($email);
    }

    /**
     * @param string $email
     *
     * @return Email
     */
    private function setEmail(string $email): Email
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("The email {$email} format is wrong");
        }

        $this->email = $email;

        return $this;
    }

    /**
     * @param Email $email
     *
     * @return bool
     */
    public function equals(Email $email): bool
    {
        return $this->toString() === $email->toString();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}