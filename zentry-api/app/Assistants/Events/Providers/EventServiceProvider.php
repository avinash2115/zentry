<?php

namespace App\Assistants\Events\Providers;

use App\Assistants\Events\EventRegistry;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 *
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }

    /**
     *
     */
    public function register(): void
    {
        $this->registerSingletones();
    }

    /**
     *
     */
    private function registerSingletones(): void
    {
        $this->app->singleton(EventRegistry::class, EventRegistry::class);
    }

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array
     */
    protected function discoverEventsWithin()
    {
        return [
            $this->app->path(
                'Assistants' . DIRECTORY_SEPARATOR . 'Elastic' . DIRECTORY_SEPARATOR . 'Events' . DIRECTORY_SEPARATOR . 'Listeners'
            ),
            $this->app->path(
                'Components' . DIRECTORY_SEPARATOR . 'Sessions' . DIRECTORY_SEPARATOR . 'Session' . DIRECTORY_SEPARATOR . 'Events' . DIRECTORY_SEPARATOR . 'Listeners'
            ),
            $this->app->path(
                'Components' . DIRECTORY_SEPARATOR . 'Users' . DIRECTORY_SEPARATOR . 'User' . DIRECTORY_SEPARATOR . 'Events' . DIRECTORY_SEPARATOR . 'Listeners'
            ),
            $this->app->path(
                'Components' . DIRECTORY_SEPARATOR . 'Users' . DIRECTORY_SEPARATOR . 'User' . DIRECTORY_SEPARATOR . 'DataProvider' . DIRECTORY_SEPARATOR . 'Events' . DIRECTORY_SEPARATOR . 'Listeners'
            ),
            $this->app->path(
                'Components' . DIRECTORY_SEPARATOR . 'Users' . DIRECTORY_SEPARATOR . 'PasswordReset' . DIRECTORY_SEPARATOR . 'Events' . DIRECTORY_SEPARATOR . 'Listeners'
            ),
            $this->app->path(
                'Components' . DIRECTORY_SEPARATOR . 'Users' . DIRECTORY_SEPARATOR . 'Team' . DIRECTORY_SEPARATOR . 'Request' . DIRECTORY_SEPARATOR . 'Events' . DIRECTORY_SEPARATOR . 'Listeners'
            ),
        ];
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }
}
