<?php

namespace App\Components\Users\Tests\Unit\Traits\DataProvider;

use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
use RuntimeException;

/**
 * Trait HelperTrait
 *
 * @package App\Components\Users\Tests\Unit\Traits\DataProvider
 */
trait HelperTrait
{
    /**
     * @param                              $status
     * @param DataProviderReadonlyContract $entity
     *
     * @throws RuntimeException
     */
    private function validateStatus(int $status, DataProviderReadonlyContract $entity): void
    {
        switch ($status) {
            case DataProviderReadonlyContract::STATUS_DISABLED:
                self::assertTrue($entity->isDisabled());
            break;
            case DataProviderReadonlyContract::STATUS_ENABLED:
                self::assertTrue($entity->isEnabled());
            break;
            case DataProviderReadonlyContract::STATUS_NOT_AUTHORIZED:
                self::assertTrue($entity->isNotAuthorized());
            break;
            default:
                throw new RuntimeException();
            break;
        }
    }
}
