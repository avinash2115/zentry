<?php

namespace App\Components\Sessions\Providers;

use App;
use App\Components\Sessions\Console\Elastic\Indexing\Index as SessionIndex;
use App\Components\Sessions\Console\PostProcess;
use App\Components\Sessions\Console\Convert as CommandConvert;
use App\Components\Sessions\Services\Note\NoteService;
use App\Components\Sessions\Services\Note\NoteServiceContract;
use App\Components\Sessions\Services\Goal\GoalService;
use App\Components\Sessions\Services\Goal\GoalServiceContract;
use App\Components\Sessions\Services\Poi\Indexable\IndexableService as PoiIndexableService;
use App\Components\Sessions\Services\Poi\Indexable\IndexableServiceContract as PoiIndexableServiceContract;
use App\Components\Sessions\Services\Poi\Participant\ParticipantService;
use App\Components\Sessions\Services\Poi\Participant\ParticipantServiceContract;
use App\Components\Sessions\Services\Poi\PoiService;
use App\Components\Sessions\Services\Poi\PoiServiceContract;
use App\Components\Sessions\Session\SOAP\SOAPContract;
use App\Components\Sessions\Session\SOAP\SOAPEntity;
use App\Components\Sessions\Services\SOAP\SOAPServiceContract;
use App\Components\Sessions\Services\SOAP\SOAPService;
use App\Components\Sessions\Services\SessionService;
use App\Components\Sessions\Services\SessionServiceContract;
use App\Components\Sessions\Services\Stream\StreamService;
use App\Components\Sessions\Services\Stream\StreamServiceContract;
use App\Components\Sessions\Services\Transcription\TranscriptionService;
use App\Components\Sessions\Services\Transcription\TranscriptionServiceContract;
use App\Components\Sessions\Session\Note\NoteContract;
use App\Components\Sessions\Session\Note\NoteEntity;
use App\Components\Sessions\Services\Progress\ProgressService as SessionProgressService;
use App\Components\Sessions\Services\Progress\ProgressServiceContract as SessionProgressServiceContract;
use App\Components\Sessions\Session\Goal\GoalContract;
use App\Components\Sessions\Session\Goal\GoalEntity;
use App\Components\Sessions\Session\Poi\Participant\ParticipantContract as PoiParticipantContract;
use App\Components\Sessions\Session\Poi\Participant\ParticipantEntity as PoiParticipantEntity;
use App\Components\Sessions\Session\Poi\PoiContract;
use App\Components\Sessions\Session\Poi\PoiEntity;
use App\Components\Sessions\Session\Progress\ProgressContract as SessionProgressContract;
use App\Components\Sessions\Session\Progress\ProgressEntity as SessionProgressEntity;
use App\Components\Sessions\Session\Repository\SessionRepositoryContract;
use App\Components\Sessions\Session\Repository\SessionRepositoryDoctrine;
use App\Components\Sessions\Session\Repository\SessionRepositoryMemory;
use App\Components\Sessions\Session\SessionContract;
use App\Components\Sessions\Session\SessionEntity;
use App\Components\Sessions\Session\Stream\StreamContract;
use App\Components\Sessions\Session\Stream\StreamEntity;
use App\Components\Sessions\Session\Transcription\Repository\TranscriptionRepositoryContract;
use App\Components\Sessions\Session\Transcription\Repository\TranscriptionRepositoryMemory;
use App\Components\Sessions\Session\Transcription\Repository\TranscriptionRepositoryODM;
use App\Components\Sessions\Session\Transcription\TranscriptionContract;
use App\Components\Sessions\Session\Transcription\TranscriptionEntity;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;

/**
 * Class SessionServiceProvider
 */
class SessionServiceProvider extends BaseServiceProvider
{
    private const MODULE_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;

    /**
     * @var string
     */
    protected string $modulePath = self::MODULE_PATH;

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws PropertyNotInit
     */
    public function boot(): void
    {
        $this->bootMigrations();
        $this->bootRoutes();
        $this->bootChannels();
        $this->bootCommands();
    }

    /**
     *
     */
    private function bootCommands(): void
    {
        $this->commands(
            [
                PostProcess::class,
                CommandConvert::class,
                SessionIndex::class
            ]
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerEntities();
        $this->registerServices();
        $this->registerRepositories();
    }

    /**
     * Register repositories
     *
     * @return void
     */
    protected function registerEntities(): void
    {
        $this->app->bind(SessionContract::class, SessionEntity::class);
        $this->app->bind(NoteContract::class, NoteEntity::class);
        $this->app->bind(SOAPContract::class, SOAPEntity::class);
        $this->app->bind(GoalContract::class, GoalEntity::class);
        $this->app->bind(SessionProgressContract::class, SessionProgressEntity::class);
        $this->app->bind(PoiContract::class, PoiEntity::class);
        $this->app->bind(PoiParticipantContract::class, PoiParticipantEntity::class);
        $this->app->bind(StreamContract::class, StreamEntity::class);

        $this->app->bind(TranscriptionContract::class, TranscriptionEntity::class);
    }

    /**
     *
     */
    protected function registerServices(): void
    {
        $this->app->bind(SessionServiceContract::class, SessionService::class);
        $this->app->bind(NoteServiceContract::class, NoteService::class);
        $this->app->bind(SOAPServiceContract::class, SOAPService::class);
        $this->app->bind(StreamServiceContract::class, StreamService::class);
        $this->app->bind(GoalServiceContract::class, GoalService::class);
        $this->app->bind(SessionProgressServiceContract::class, SessionProgressService::class);
        $this->app->bind(PoiServiceContract::class, PoiService::class);
        $this->app->bind(PoiIndexableServiceContract::class, PoiIndexableService::class);
        $this->app->bind(ParticipantServiceContract::class, ParticipantService::class);

        $this->app->bind(TranscriptionServiceContract::class, TranscriptionService::class);
    }

    /**
     * Register repositories
     *
     * @return void
     */
    protected function registerRepositories(): void
    {
        if (App::runningUnitTests()) {
            $this->app->singleton(TranscriptionRepositoryContract::class, TranscriptionRepositoryMemory::class);
            $this->app->singleton(SessionRepositoryContract::class, SessionRepositoryMemory::class);
        } else {
            $this->app->singleton(TranscriptionRepositoryContract::class, TranscriptionRepositoryODM::class);
            $this->app->singleton(SessionRepositoryContract::class, SessionRepositoryDoctrine::class);
        }
    }
}
