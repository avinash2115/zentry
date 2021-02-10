<?php

namespace App\Components\Users\ValueObjects\DataProvider\Sync;

use App\Convention\Contracts\Arrayable;
use Arr;

/**
 * Class Event
 *
 * @package App\Components\Users\ValueObjects\DataProvider\Sync
 */
final class Participant implements Arrayable
{
    /**
     * @var string
     */
    private string $email;

    /**
     * @var string|null
     */
    private ?string $firstName = null;

    /**
     * @var string|null
     */
    private ?string $lastName = null;

    /**
     * @param string $email
     * @param string $displayName
     */
    public function __construct(string $email, string $displayName = null)
    {
        $this->email = $email;

        if ($displayName !== null && !strEmpty($displayName)) {
            $exploded = explode(' ', $displayName);

            $this->firstName = Arr::get($exploded, 0);
            $this->lastName = Arr::get($exploded, 1);
        }
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function firstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function lastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'email' => $this->email(),
            'first_name' => $this->firstName(),
            'last_name' => $this->lastName(),
        ];
    }
}
