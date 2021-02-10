<?php

namespace App\Components\Users\Console\CRM;

use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use Exception;
use Flusher;
use Illuminate\Console\Command;
use Illuminate\Contracts\Debug\ExceptionHandler;

/**
 * Class ConnectionCheck
 *
 * @package App\Components\Users\Console\CRM
 */
class ConnectionCheck extends Command
{
    use UserServiceTrait;

    public const SIGNATURE = 'users:crm:connection:test';

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
    protected $description = "Update the user's crm availability";

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->info("[{$this->signature}] started ...");

        Flusher::open();

        collect(CRMReadonlyContract::AVAILABLE_DRIVERS)->map(function ($driver) {
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

                            $crmService = $this->userService__()->workWith($user->identity())->crmService()->workWith(
                                $crm->identity()
                            );
                            $active = false;
                            try {
                                $crmService->check();
                                $active = true;
                            } catch (Exception $exception) {
                                app(ExceptionHandler::class)->report($exception);
                            }
                            $crmService->change(['active' => $active]);

                            Flusher::flush();
                        });
                }
            );
        });

        Flusher::commit();

        $this->info("[{$this->signature}] finished.");
    }
}
