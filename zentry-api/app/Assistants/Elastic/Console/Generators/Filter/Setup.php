<?php

namespace App\Assistants\Elastic\Console\Generators\Filter;

use App\Assistants\Elastic\Services\Setup\Traits\FilterServiceTrait;
use App\Console\Command;

/**
 * Class Setup
 *
 * @package App\Assistants\Elastic\Console\Generators\Filter
 */
class Setup extends Command
{
    use FilterServiceTrait;

    public const SIGNATURE = 'generators:elastic:index:filters:setup';

    /**
     * @var string
     */
    protected $signature = self::SIGNATURE . self::OPTION_MEMORY;

    /**
     * @var string
     */
    protected $description = 'Create elastic-index with mappings';

    /**
     *
     */
    public function handle(): void
    {
        $this->_handle(
            function () {
                $this->filterService__()->setup();
            }
        );
    }
}
