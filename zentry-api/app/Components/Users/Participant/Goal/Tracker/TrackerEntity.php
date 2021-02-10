<?php

namespace App\Components\Users\Participant\Goal\Tracker;

use App\Components\Users\Participant\Goal\GoalContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Entities\Traits\HasCreatedAtTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class TrackerEntity
 *
 * @package App\Components\Users\Participant\Goal\Tracker
 */
class TrackerEntity implements TrackerContract
{
    use IdentifiableTrait;
    use HasCreatedAtTrait;
    use CollectibleTrait;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var string
     */
    private string $icon;

    /**
     * @var string
     */
    private string $color;

    /**
     * @var GoalContract
     */
    private GoalContract $goal;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $sessions;

    /**
     * @param Identity     $identity
     * @param GoalContract $goal
     * @param string       $name
     * @param string       $type
     * @param string       $icon
     * @param string       $color
     *
     * @throws Exception
     */
    public function __construct(
        Identity $identity,
        GoalContract $goal,
        string $name,
        string $type = self::TYPE_NEUTRAL,
        string $icon = 'life-ring',
        string $color = '#ffc107'
    ) {
        $this->setIdentity($identity);

        $this->setGoal($goal)->setName($name)->setIcon($icon)->changeType($type)->changeColor($color);

        $this->sessions = new ArrayCollection();

        $this->setCreatedAt();
    }

    /**
     * @inheritDoc
     */
    public function changeName(string $value): TrackerContract
    {
        return $this->setName($value);
    }

    /**
     * @inheritDoc
     */
    public function changeIcon(?string $value): TrackerContract
    {
        return $this->setIcon($value);
    }

    /**
     * @inheritDoc
     */
    public function sessions(): Collection
    {
        return $this->doctrineCollectionToCollection($this->sessions);
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function changeType(string $value): TrackerContract
    {
        if (!in_array($value, self::TYPES_AVAILABLE, true)) {
            throw new InvalidArgumentException("Type {$value} is not allowed");
        }

        $this->type = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function icon(): string
    {
        return $this->icon;
    }

    /**
     * @inheritDoc
     */
    public function color(): string
    {
        return $this->color;
    }

    /**
     * @inheritDoc
     */
    public function changeColor(string $value): TrackerContract
    {
        $this->color = $value;

        return $this;
    }

    /**
     * @param GoalContract $goal
     *
     * @return TrackerEntity
     */
    private function setGoal(GoalContract $goal): TrackerEntity
    {
        $this->goal = $goal;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return TrackerEntity
     */
    private function setName(string $name): TrackerEntity
    {
        if (strEmpty($name)) {
            throw new InvalidArgumentException("Name can't be empty");
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @param string $icon
     *
     * @return TrackerEntity
     */
    private function setIcon(string $icon): TrackerEntity
    {
        if (strEmpty($icon)) {
            throw new InvalidArgumentException("Icon can't be empty");
        }

        $this->icon = $icon;

        return $this;
    }
}
