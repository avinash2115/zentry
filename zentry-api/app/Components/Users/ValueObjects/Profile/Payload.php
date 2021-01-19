<?php

namespace App\Components\Users\ValueObjects\Profile;

use App\Components\Users\User\Profile\ProfileContract;
use App\Convention\Contracts\Arrayable;
use InvalidArgumentException;

/**
 * Class Payload
 *
 * @package App\Components\Users\ValueObjects\Profile
 */
final class Payload implements Arrayable
{
    public const DEFAULT_PHONE_CODE = '1';

    /**
     * @var string
     */
    private string $firstName;

    /**
     * @var string
     */
    private string $lastName;

    /**
     * @var string|null
     */
    private ?string $phoneCode;

    /**
     * @var string|null
     */
    private ?string $phoneNumber;

    /**
     * CreationPayload constructor.
     *
     * @param string      $firstName
     * @param string      $lastName
     * @param string|null $phoneCode
     * @param string|null $phoneNumber
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $firstName,
        string $lastName,
        ?string $phoneCode = null,
        ?string $phoneNumber = null
    ) {
        $this->setFirstName($firstName)->setLastName($lastName);

        $this->setPhoneCode($phoneCode);
        $this->setPhoneNumber($phoneNumber);
    }

    /**
     * @return string
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return Payload
     * @throws InvalidArgumentException
     */
    private function setFirstName(string $firstName): Payload
    {
        if (strEmpty($firstName)) {
            throw new InvalidArgumentException('First Name cannot be empty');
        }

        $this->firstName = trim($firstName);

        return $this;
    }

    /**
     * @return string
     */
    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return Payload
     * @throws InvalidArgumentException
     */
    private function setLastName(string $lastName): Payload
    {
        if (strEmpty($lastName)) {
            throw new InvalidArgumentException('Last Name cannot be empty');
        }

        $this->lastName = trim($lastName);

        return $this;
    }

    /**
     * @return string|null
     */
    public function phoneCode(): ?string
    {
        return $this->phoneCode;
    }

    /**
     * @param string|null $phoneCode
     *
     * @return Payload
     */
    private function setPhoneCode(?string $phoneCode = null): Payload
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     *
     * @return Payload
     */
    private function setPhoneNumber(?string $phoneNumber = null): Payload
    {
        if ($phoneNumber !== null && !strEmpty($phoneNumber) && $this->phoneCode() === null) {
            $this->setPhoneCode(self::DEFAULT_PHONE_CODE);
        }

        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName(),
            'last_name' => $this->lastName(),
            'phone_code' => $this->phoneCode(),
            'phone_number' => $this->phoneNumber(),
        ];
    }
}
