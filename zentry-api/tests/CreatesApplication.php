<?php

namespace Tests;

use Artisan;
use Illuminate\Contracts\Console\Kernel;

/**
 * Trait CreatesApplication
 *
 * @package Tests
 */
trait CreatesApplication
{
    public static bool $seeded = false;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $this->seedDatabase();

        return $app;
    }

    /**
     * Run seeder one time
     */
    protected function seedDatabase(): void
    {
        if (!static::$seeded) {
            $this->afterApplicationCreated(
                function () {
                    static::$seeded = true;
                    Artisan::call('migrate:fresh');
                }
            );
        }
    }
}
