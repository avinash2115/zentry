<?php

namespace App\Http\Middleware\Access\Shared;

use App\Components\Share\Services\Shared\SharedResolvedService;
use App\Components\Share\Services\Shared\Traits\SharedServiceTrait;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Generators\Identity\IdentityGenerator;
use Closure;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Authenticate
 *
 * @package App\Http\Middleware\Access\Shared
 */
class Authenticate
{
    use SharedServiceTrait;
    use AuthServiceTrait;
    use UserServiceTrait;

    const HEADER = 'X-SHARED-ID';

    const ALIAS = 'auth.shared';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException|RuntimeException
     * @throws InvalidArgumentException
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
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws PermissionDeniedException
     */
    public function validate(Request $request): bool
    {
        $route = $request->route();

        if ($route instanceof Route) {
            $id = $request->header(self::HEADER, '');

            if (is_string($id) && IdentityGenerator::isValid($id) && count($route->parameters()) > 0) {
                $shared = $this->sharedService__()->workWith($id)->readonly();

                $cleanUri = Str::before($request->getRequestUri(), '?');

                switch (true) {
                    case count(array_diff($route->parameters(), $shared->payload()->parameters())) > 0 && count(
                            array_diff($shared->payload()->parameters(), $route->parameters())
                        ):
                    case !in_array($request->method(), $shared->payload()->methods(), true):
                    case !Str::startsWith($cleanUri, $shared->payload()->pattern()) && !Str::startsWith(
                            $shared->payload()->pattern(),
                            $cleanUri
                        ):
                        denied('This action is not permitted.');
                    break;
                    default:
                        app()->make(SharedResolvedService::class)->workWithSharable($shared);
                        break;
                }
            } elseif (!collect($route->middleware())->contains('jwt-auth')) {
                unauthorized('Shared reference is not valid');
            }

            return true;
        }

        throw new UnexpectedValueException('Cannot obtain route');
    }
}
