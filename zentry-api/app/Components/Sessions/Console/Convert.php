<?php

namespace App\Components\Sessions\Console;

use App\Components\Sessions\Jobs\Stream\Audio\Convert as JobConvert;
use App\Components\Sessions\Session\Repository\SessionRepositoryContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use Exception;
use Flusher;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class Convert
 *
 * @package App\Components\Sessions\Console
 */
class Convert extends Command
{
    public const SIGNATURE = 'sessions:convert';

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
    protected $description = 'Transcode the recording';

    /**
     * @return void
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        $this->info("[{$this->signature}] started ...");

        app()->make(SessionRepositoryContract::class)->filterByStatuses([SessionReadonlyContract::STATUS_WRAPPED])->getAll()->each(
            function (SessionReadonlyContract $session) {
                $this->info("[{$this->signature}] processing {$session->name()} ... ");

                try {
                    dispatch_now(new JobConvert($session->identity()));

                    Flusher::clear();
                } catch (Exception $exception) {
                    app(ExceptionHandler::class)->report($exception);
                }
            }
        );

        $this->info("[{$this->signature}] finished.");
    }
}
