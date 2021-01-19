<?php

namespace App\Components\Users\Participant\Goal;

use App\Components\Users\Participant\Goal\Tracker\TrackerContract;
use App\Components\Users\Participant\IEP\IEPReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;

/**
 * Interface GoalContract
 *
 * @package App\Components\Users\Participant\Goal
 */
interface GoalContract extends GoalReadonlyContract
{
    /**
     * @return GoalContract
     */
    public function reach(): GoalContract;

    /**
     * @return GoalContract
     */
    public function unReach(): GoalContract;

    /**
     * @param string $name
     *
     * @return GoalContract
     */
    public function changeName(string $name): GoalContract;

    /**
     * @param string $description
     *
     * @return GoalContract
     */
    public function changeDescription(string $description): GoalContract;

    /**
     * @param TrackerContract $tracker
     *
     * @return GoalContract
     */
    public function addTracker(TrackerContract $tracker): GoalContract;

    /**
     * @param Identity $identity
     *
     * @return trackerContract
     * @throws NotFoundException
     */
    public function trackerByIdentity(Identity $identity): TrackerContract;

    /**
     * @param TrackerContract $tracker
     *
     * @return GoalContract
     * @throws NotFoundException
     */
    public function removeTracker(TrackerContract $tracker): GoalContract;

    /**
     * @param IEPReadonlyContract|null $value
     *
     * @return GoalContract
     */
    public function changeIEP(?IEPReadonlyContract $value = null): GoalContract;
}
