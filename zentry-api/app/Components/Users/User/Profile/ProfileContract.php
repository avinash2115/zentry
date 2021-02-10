<?php

namespace App\Components\Users\User\Profile;

use InvalidArgumentException;

/**
 * Interface ProfileContract
 *
 * @package App\Components\Users\User\Profile
 */
interface ProfileContract extends ProfileReadonlyContract
{
    /**
     * @param string $name
     *
     * @return ProfileContract
     * @throws InvalidArgumentException
     */
    public function changeFirstName(string $name): ProfileContract;

    /**
     * @param string $name
     *
     * @return ProfileContract
     * @throws InvalidArgumentException
     */
    public function changeLastName(string $name): ProfileContract;

    /**
     * @param string $phoneCode
     *
     * @return ProfileContract
     */
    public function changePhoneCode(string $phoneCode): ProfileContract;

    /**
     * @param string $phoneNumber
     *
     * @return ProfileContract
     */
    public function changePhoneNumber(string $phoneNumber): ProfileContract;
}
