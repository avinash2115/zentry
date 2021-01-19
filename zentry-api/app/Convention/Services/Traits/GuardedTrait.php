<?php

namespace App\Convention\Services\Traits;

use App\Components\Share\Contracts\SharableContract;
use App\Components\Share\Services\Shared\SharedResolvedService;
use App\Components\Share\Shared\SharedReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Log;
use UnexpectedValueException;

/**
 * Trait GuardedTrait
 *
 * @package App\Convention\Services\Traits
 */
trait GuardedTrait
{
    use AuthServiceTrait;

    /**
     * @param mixed    $service
     * @param callable $authCallback
     * @param callable $sharedCallback
     * @param UserReadonlyContract|null $consoleUser
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     * @throws PropertyNotInit
     */
    final protected function guardRepository($service, callable $authCallback, callable $sharedCallback, UserReadonlyContract $consoleUser = null): void
    {
        if (!app()->runningInConsole()) {
            if ($consoleUser instanceof UserReadonlyContract) {
                Log::warning("Passed {$consoleUser->identity()->toString()} while app is not running in console ...");
            }

            $shared = null;

            if ($service instanceof SharableContract) {
                try {
                    $shared = app()->make(SharedResolvedService::class)->readonly();
                } catch (PropertyNotInit $exception) {}
            }

            switch (true) {
                case $shared instanceof SharedReadonlyContract:
                    $sharedCallback($shared);
                break;
                case $this->authService__()->check():
                    $authCallback($this->authService__()->user()->readonly());
                break;
            }
        } else {
            if ($consoleUser instanceof UserReadonlyContract) {
                $authCallback($consoleUser);
            }
        }
    }

    /**
     * @param mixed    $service
     * @param callable $callback
     *
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PermissionDeniedException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    final protected function guardEntity($service, callable $callback): void
    {
        if (!app()->runningInConsole()) {
            $shared = null;

            if ($service instanceof SharableContract) {
                try {
                    $shared = app()->make(SharedResolvedService::class)->readonly();
                } catch (PropertyNotInit $exception) {}
            }

            switch (true) {
                case $shared instanceof SharedReadonlyContract:
                    $isAcceptedType = in_array($shared->type(), $service->types());
                    $isEqualsPayload = $service->payload()->equals($shared->payload());
                    $isContainsPayload = $service->payload()->contains($shared->payload());

                    if (!$isAcceptedType || (!$isEqualsPayload && !$isContainsPayload) ) {
                        throw new PermissionDeniedException('Access Restricted');
                    }
                break;
                case $this->authService__()->check():
                    if (!$callback($this->authService__()->user()->readonly())) {
                        throw new PermissionDeniedException('Access Restricted');
                    }
                break;
            }
        }
    }
}
