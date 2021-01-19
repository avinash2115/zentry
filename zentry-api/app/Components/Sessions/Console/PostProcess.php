<?php

namespace App\Components\Sessions\Console;

use App\Components\Sessions\Jobs\PostProcess as JobPostProcess;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Repository\SessionRepositoryContract;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use Exception;
use Flusher;
use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * Class PostProcess
 *
 * @package App\Components\Sessions\Console
 */
class PostProcess extends Command
{
    public const SIGNATURE = 'sessions:post-process';

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
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public function handle(): void
    {
        $this->info("[{$this->signature}] started ...");

        app()->make(SessionRepositoryContract::class)->filterByStatuses([SessionReadonlyContract::STATUS_WRAPPED])->getAll()->filter(
            static function (SessionReadonlyContract $session) {
                return $session->thumbnail() === null || $session->streams()->some(
                        static function (StreamReadonlyContract $stream) {
                            return !$stream->isConverted();
                        }
                    ) || $session->pois()->some(
                        static function (PoiReadonlyContract $poi) {
                            return !$poi->isConverted();
                        }
                    );
            }
        )->each(
            function (SessionReadonlyContract $session) {
                $this->info("[{$this->signature}] processing {$session->name()} ... ");

                try {
                    dispatch_now(new JobPostProcess($session->identity()));

                    Flusher::clear();
                } catch (Exception $exception) {
                    app(ExceptionHandler::class)->report($exception);
                }
            }
        );

        $this->info("[{$this->signature}] finished.");
    }
}
