<?php

namespace App\Convention\ValueObjects\Identity;

use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Contracts\Stringable;
use InvalidArgumentException;

/**
 * Class Identity
 *
 * @package App\Convention\ValueObjects\Identity
 */
final class Identity implements Stringable
{
    /**
     * @var string
     */
    private string $id;

    /**
     * @param string $id
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $id)
    {
        if (!IdentityGenerator::isValid($id)) {
            throw new InvalidArgumentException('Incorrect Identity format, should be UUIDv4');
        }

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param Identity $id
     *
     * @return bool
     */
    public function equals(Identity $id): bool
    {
        return strtolower((string)$this) === strtolower((string)$id);
    }
}