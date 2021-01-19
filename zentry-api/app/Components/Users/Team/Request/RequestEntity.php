<?php

namespace App\Components\Users\Team\Request;

use App\Components\Users\Team\TeamContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Traits\HasCreatedAtTrait;
use App\Convention\Entities\Traits\IdentifiableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;

/**
 * Class RequestEntity
 *
 * @package App\Components\Users\Team\Request
 */
class RequestEntity implements RequestContract
{
    use HasCreatedAtTrait;
    use IdentifiableTrait;

    /**
     * @var UserReadonlyContract
     */
    private UserReadonlyContract $user;

    /**
     * @var TeamContract
     */
    private TeamContract $team;

    /**
     * @param UserReadonlyContract $user
     * @param TeamContract         $team
     * @param Identity             $identity
     *
     * @throws Exception
     */
    public function __construct(
        UserReadonlyContract $user,
        TeamContract $team,
        Identity $identity
    ) {
        $this->setIdentity($identity);
        $this->setUser($user);
        $this->setCreatedAt();

        $this->team = $team;
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @return RequestEntity
     */
    private function setUser(UserReadonlyContract $user): RequestEntity
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function user(): UserReadonlyContract
    {
        return $this->user;
    }
}
