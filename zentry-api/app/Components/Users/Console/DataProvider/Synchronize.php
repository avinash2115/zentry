<?php

namespace App\Components\Users\Console\DataProvider;

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Console\Command;
use Exception;
use Flusher;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * Class Synchronize
 *
 * @package App\Components\Users\Console\DataProvider
 */
class Synchronize extends Command
{
    use UserServiceTrait;

    public const SIGNATURE = 'data-provider:entities:sync';

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
    protected $description = "Update the user's data providers";

    /**
     *
     */
    public function handle(): void
    {
        $this->_handle(
            function () {
                Flusher::open();

                collect(DataProviderReadonlyContract::DRIVERS_AVAILABLE)->map(
                    function (string $label, string $driver) {
                        $this->userService__()->applyFilters(
                            [
                                'data_providers' => [
                                    'drivers' => [
                                        'collection' => [$driver],
                                        'has' => true,

                                    ],
                                    'statuses' => [
                                        'collection' => [DataProviderReadonlyContract::STATUS_ENABLED],
                                        'has' => true,
                                    ],
                                ],
                            ]
                        );

                        $this->userService__()->listRO()->each(
                            function (UserReadonlyContract $user) {
                                $user->dataProviders()->each(
                                    function (DataProviderReadonlyContract $dataProvider) use ($user) {
                                        try {
                                            $this->userService__()
                                                ->workWith($user->identity())
                                                ->dataProviderService()
                                                ->workWith(
                                                    $dataProvider->identity()
                                                )->sync();
                                        } catch (Exception $exception) {
                                            app(ExceptionHandler::class)->report($exception);
                                        }

                                        Flusher::flush();
                                    }
                                );
                            }
                        );
                    }
                );

                Flusher::commit();
            }
        );
    }
}
