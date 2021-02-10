<?php

namespace App\Assistants\Elastic\ValueObjects\Filter\Contracts;

use Exception;

/**
 * Interface NeedlePresenter
 *
 * @package App\Assistants\Elastic\ValueObjects\Search
 */
interface NeedlePresenter
{
    /**
     * @return array
     * @throws Exception
     */
    public function present(): array;
}
