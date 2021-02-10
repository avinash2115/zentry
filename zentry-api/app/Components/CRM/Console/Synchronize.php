<?php

namespace App\Components\CRM\Console;

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use Exception;
use Flusher;
use Illuminate\Console\Command;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * Class Synchronize
 *
 * @package App\Components\CRM\Console
 */
class Synchronize extends Command
{
    use UserServiceTrait;

    public const SIGNATURE = 'crm:entities:sync';

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
    protected $description = "Update the user's crm entities";

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->info("[{$this->signature}] started ...");

        Flusher::open();

        collect(CRMReadonlyContract::AVAILABLE_DRIVERS)->map(
            function ($driver) {
                $this->userService__()->applyFilters(
                    [
                        'crms' => [
                            'driver' => $driver,
                            'has' => true,
                        ],
                    ]
                );

                $this->userService__()->listRO()->each(
                    function (UserReadonlyContract $user) {
                        $user->crms()->each(
                            function (CRMReadonlyContract $crm) use ($user) {
                                $this->info("Check connection {$crm->driver()} for user {$user->identity()}.");

                                try {
                                    $crmService = $this->userService__()->workWith($user->identity())->crmService()->workWith(
                                        $crm->identity()
                                    );
                                    $crmService->sync();
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

        $this->info("[{$this->signature}] finished.");
    }
}
