<?php

namespace App\Components\Users\Providers\Auth;

use App\Components\Users\Services\Auth\AuthService;
use App\Components\Users\Services\Auth\AuthServiceContract;
use App\Components\Users\Services\Auth\AuthUserService;
use App\Components\Users\Services\Auth\AuthUserServiceContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Class AuthServiceProvider
 *
 * @package App\Components\Users\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::provider(
            AuthServiceUserProvider::NAME,
            function ($app) {
                return $app->make(AuthServiceUserProvider::class);
            }
        );
    }

    public function register(): void
    {
        $this->registerServices();
    }

    /**
     *
     */
    protected function registerServices(): void
    {
        $this->app->singleton(AuthServiceContract::class, AuthService::class);
        $this->app->singleton(AuthUserServiceContract::class, AuthUserService::class);
    }
}
