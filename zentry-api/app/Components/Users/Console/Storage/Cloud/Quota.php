<?php

namespace App\Components\Users\Console\Storage\Cloud;

use App\Assistants\Files\Services\Traits\CloudFileServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use Exception;
use Flusher;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * Class Quota
 *
 * @package App\Components\Users\Console\Storage\Cloud
 */
class Quota extends Command
{
    use UserServiceTrait;
    use CloudFileServiceTrait;

    public const SIGNATURE = 'sessions:storage:cloud:quota:sync';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = self::SIGNATURE;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update the user's storage usage and capacity info";

    /**
     * @return void
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        $this->info("[{$this->signature}] started ...");

        Flusher::open();

        $this->userService__()->applyFilters(
            [
                'storages' => [
                    'driver' => StorageReadonlyContract::KLOUDLESS_GROUP,
                    'has' => true,
                    'enabled' => true,
                ],
            ]
        );

        $this->userService__()->listRO()->each(
            function (UserReadonlyContract $user) {
                $user->storages()->filter(
                    static function (StorageReadonlyContract $storage) {
                        return in_array($storage->driver(), StorageReadonlyContract::KLOUDLESS_GROUP, true);
                    }
                )->each(
                    function (StorageReadonlyContract $storage) use ($user) {
                        $this->info("Processing storage {$storage->driver()} quota for user {$user->identity()}.");

                        try {
                            $this->userService__()->workWith($user->identity())->storageService()->workWith(
                                $storage->identity()
                            )->sync();
                        } catch (Exception $exception) {
                            app(ExceptionHandler::class)->report($exception);
                        }

                        Flusher::flush();
                    }
                );
            }
        );

        Flusher::commit();

        $this->info("[{$this->signature}] finished.");
    }
}
