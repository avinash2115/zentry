<?php

namespace App\Components\Users\Participant\Goal\Tracker;

use InvalidArgumentException;

/**
 * Interface TrackerContract
 *
 * @package App\Components\Users\Participant\Goal\Tracker
 */
interface TrackerContract extends TrackerReadonlyContract
{
    /**
     * @param string $value
     *
     * @return TrackerContract
     */
    public function changeName(string $value): TrackerContract;

    /**
     * @param string $value
     *
     * @return TrackerContract
     * @throws InvalidArgumentException
     */
    public function changeType(string $value): TrackerContract;

    /**
     * @param string $value
     *
     * @return TrackerContract
     */
    public function changeIcon(string $value): TrackerContract;

    /**
     * @param string $value
     *
     * @return TrackerContract
     */
    public function changeColor(string $value): TrackerContract;
}
