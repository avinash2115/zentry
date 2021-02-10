<?php

namespace App\Components\Users\Services\Team\Traits;

use App\Components\Users\Services\Team\TeamServiceContract;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * Trait TeamServiceTrait
 *
 * @package App\Components\Users\Services\Team\Traits
 */
trait TeamServiceTrait
{
    /**
     * @var TeamServiceContract | null
     */
    private ?TeamServiceContract $teamService__ = null;

    /**
     * @return TeamServiceContract
     * @throws BindingResolutionException
     */
    private function teamService__(): TeamServiceContract
    {
        if (!$this->teamService__ instanceof TeamServiceContract) {
            $this->teamService__ = app()->make(TeamServiceContract::class);
        }

        return $this->teamService__;
    }
}
