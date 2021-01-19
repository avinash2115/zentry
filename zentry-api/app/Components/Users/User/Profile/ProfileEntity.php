<?php

namespace App\Components\Users\User\Profile;

use App\Components\Users\User\UserContract;
use App\Components\Users\ValueObjects\Profile\Payload;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\Entities\Traits\TimestampableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use InvalidArgumentException;

/**
 * Class ProfileEntity
 *
 * @package App\Components\Users\User\Profile
 */
class ProfileEntity implements ProfileContract
{
    use IdentifiableTrait;
    use TimestampableTrait;

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
     * @var UserContract
     */
    private UserContract $user;

    /**
     * ProfileEntity constructor.
     *
     * @param UserContract $user
     * @param Identity     $identity
     * @param Payload      $payload
     *
     * @throws InvalidArgumentException|Exception
     */
    public function __construct(
        UserContract $user,
        Identity $identity,
        Payload $payload
    ) {
        $this->setIdentity($identity);
        $this->setFirstName($payload->firstName())->setLastName($payload->lastName())->setPhoneCode($payload->phoneCode())->setPhoneNumber($payload->phoneNumber());

        $this->setUser($user);
        $this->setCreatedAt();
        $this->setUpdatedAt();
    }

    /**
     * @inheritDoc
     */
    public function firstName(): string
    {
        return $this->firstName;
    }

    /**
     * @inheritDoc
     */
    public function changeFirstName(string $firstName): ProfileContract
    {
        $this->setFirstName($firstName);

        return $this;
    }

    /**
     * @param string $firstName
     *
     * @return ProfileEntity
     * @throws InvalidArgumentException
     */
    private function setFirstName(string $firstName): ProfileEntity
    {
        if (strEmpty($firstName)) {
            throw new InvalidArgumentException('First name cannot be empty');
        }

        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function lastName(): string
    {
        return $this->lastName;
    }

    /**
     * @inheritDoc
     */
    public function changeLastName(string $lastName): ProfileContract
    {
        $this->setLastName($lastName);

        return $this;
    }

    /**
     * @param string $lastName
     *
     * @return ProfileEntity
     * @throws InvalidArgumentException
     */
    private function setLastName(string $lastName): ProfileEntity
    {
        if (strEmpty($lastName)) {
            throw new InvalidArgumentException('Last name cannot be empty');
        }

        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function phoneCode(): ?string
    {
        return $this->phoneCode;
    }

    /**
     * @inheritDoc
     */
    public function changePhoneCode(?string $phoneCode = null): ProfileContract
    {
        $this->setPhoneCode($phoneCode);

        return $this;
    }

    /**
     * @param string|null $phoneCode
     *
     * @return ProfileEntity
     */
    private function setPhoneCode(?string $phoneCode = null): ProfileEntity
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @inheritDoc
     */
    public function changePhoneNumber(?string $phoneNumber = null): ProfileContract
    {
        $this->setPhoneNumber($phoneNumber);

        return $this;
    }

    /**
     * @param string|null $phoneNumber
     *
     * @return ProfileEntity
     */
    private function setPhoneNumber(?string $phoneNumber = null): ProfileEntity
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function displayName(): string
    {
        return "{$this->firstName()} {$this->lastName()}";
    }

    /**
     * @param UserContract $user
     *
     * @return ProfileEntity
     */
    private function setUser(UserContract $user): ProfileEntity
    {
        $this->user = $user;

        return $this;
    }
}
