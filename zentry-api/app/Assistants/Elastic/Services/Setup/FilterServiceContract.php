<?php

namespace App\Assistants\Elastic\Services\Setup;

use App\Components\Sessions\Services\Poi\Indexable\SetupService;
use App\Components\Sessions\Services\SessionServiceContract;

/**
 * Interface FilterServiceContract
 *
 * @package App\Assistants\Elastic\Services\Setup
 */
interface FilterServiceContract
{
    public const SUBJECTS = [
        SessionServiceContract::class,
        SetupService::class
    ];

    /**
     *
     */
    public function setup(): void;
}
