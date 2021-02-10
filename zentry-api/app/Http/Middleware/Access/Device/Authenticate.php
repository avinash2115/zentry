<?php

namespace App\Http\Middleware\Access\Device;

use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Device\Traits\DeviceServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Device\ConnectingToken;
use App\Convention\Exceptions\Repository\NotFoundException;
use Cache;
use Closure;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use UnexpectedValueException;

/**
 * Class Authenticate
 *
 * @package App\Http\Middleware\Access\Device
 */
class Authenticate
{
    use DeviceServiceTrait;
    use AuthServiceTrait;
    use UserServiceTrait;

    const HEADER = 'X-DEVICE-REFERENCE';

    const ALIAS = 'auth.user.device';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException|RuntimeException
     */
    public function handle($request, Closure $next)
    {
        $this->validate($request);

        return $next($request);
    }

    /**
     * @param Request $request
     *
     * @return bool
     * @throws BindingResolutionException|NotFoundException|NonUniqueResultException|RuntimeException
     */
    public function validate(Request $request): bool
    {
        $route = $request->route();

        if ($route instanceof Route) {
            $reference = $request->header(self::HEADER);

            if (is_string($reference)) {
                $tempConnectingToken = Cache::get($reference);

                if ($tempConnectingToken instanceof ConnectingToken) {
                    try {
                        $user = $this->userService__()->workWith($tempConnectingToken->userIdentity())->readonly();
                    } catch (NotFoundException $exception) {
                        throw new UnauthorizedHttpException(
                            self::ALIAS, 'There are no users associated with this device.'
                        );
                    }
                } else {
                    try {
                        $user = $this->deviceService__()->workWithReference($reference)->readonly()->user();
                    } catch (NotFoundException $exception) {
                        throw new UnauthorizedHttpException(
                            self::ALIAS, 'Device reference is not valid or not registered in the account'
                        );
                    }
                }

                $this->loginOnceFromUserOrThrowException($user);
            } else {

                if (!collect($route->middleware())->contains('jwt-auth') || collect($route->excludedMiddleware())->contains('jwt-auth')) {
                    throw new UnauthorizedHttpException(self::ALIAS, 'Device reference is not valid');
                }
            }

            return true;
        }

        throw new UnexpectedValueException('Cannot obtain route');
    }

    /**
     * @param UserReadonlyContract $user
     *
     * @throws BindingResolutionException
     * @throws RuntimeException
     */
    private function loginOnceFromUserOrThrowException(UserReadonlyContract $user): void
    {
        if (!$this->authService__()->loginOnceFromUser($user)) {
            throw new RuntimeException('Cannot authorize because of the server error');
        }
    }
}
