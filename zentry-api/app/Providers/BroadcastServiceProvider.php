<?php

namespace App\Providers;

use App\Http\Middleware\Access\Device\Authenticate;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

/**
 * Class BroadcastServiceProvider
 * @package App\Providers
 */
class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes(['middleware' => ['web', 'jwt-auth', Authenticate::ALIAS]]);

        require base_path('routes/channels.php');
    }
}
