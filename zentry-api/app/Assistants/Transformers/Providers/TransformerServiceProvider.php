<?php

namespace App\Assistants\Transformers\Providers;

use App\Assistants\Transformers\JsonApi\LinkParameters;
use Illuminate\Support\ServiceProvider;

class TransformerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerSingletons();
    }

    private function registerSingletons(): void
    {
        $this->app->singleton(LinkParameters::class, LinkParameters::class);
    }
}
