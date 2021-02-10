<?php

namespace App\Components\CRM\Providers;

use App;
use App\Components\CRM\Console\Synchronize as SynchronizeCommand;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\CRM\Services\CRMService;
use App\Components\CRM\Services\CRMServiceContract;
use App\Components\CRM\Services\Source\SourceService;
use App\Components\CRM\Services\Source\SourceServiceContract;
use App\Components\CRM\Services\SyncLog\SyncLogService;
use App\Components\CRM\Services\SyncLog\SyncLogServiceContract;
use App\Components\CRM\Source\ParticipantGoalSourceEntity;
use App\Components\CRM\Source\ParticipantIEPSourceEntity;
use App\Components\CRM\Source\ParticipantSourceEntity;
use App\Components\CRM\Source\Repository\SourceRepositoryContract;
use App\Components\CRM\Source\Repository\SourceRepositoryDoctrine;
use App\Components\CRM\Source\Repository\SourceRepositoryMemory;
use App\Components\CRM\Source\SchoolSourceEntity;
use App\Components\CRM\Source\SessionSourceEntity;
use App\Components\CRM\Source\TeamSourceEntity;
use App\Components\CRM\SyncLog\Repository\SyncLogRepositoryContract;
use App\Components\CRM\SyncLog\Repository\SyncLogRepositoryDoctrine;
use App\Components\CRM\SyncLog\Repository\SyncLogRepositoryMemory;
use App\Components\CRM\SyncLog\SyncLogContract;
use App\Components\CRM\SyncLog\SyncLogEntity;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use App\Components\CRM\Source\ServiceSourceEntity;
use App\Components\CRM\Source\ProviderSourceEntity;

/**
 * Class CRMServiceProvider
 *
 * @package App\Components\CRM\Providers
 */
class CRMServiceProvider extends BaseServiceProvider
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
        $this->bootConfigs('crm');
        $this->bootCommands();
        $this->bootSchedule();
        $this->bootAliases();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        parent::register();
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
        $this->app->bind(SyncLogContract::class, SyncLogEntity::class);
    }

    /**
     * Register mutators
     *
     * @return void
     */
    protected function registerServices(): void
    {
        $this->app->bind(SourceServiceContract::class, SourceService::class);
        $this->app->bind(CRMServiceContract::class, CRMService::class);
        $this->app->bind(SyncLogServiceContract::class, SyncLogService::class);
    }

    /**
     * Register repositories
     *
     * @return void
     */
    protected function registerRepositories(): void
    {
        if (App::runningUnitTests()) {
            $this->app->singleton(SourceRepositoryContract::class, SourceRepositoryMemory::class);
            $this->app->singleton(SyncLogRepositoryContract::class, SyncLogRepositoryMemory::class);
        } else {
            $this->app->singleton(SourceRepositoryContract::class, SourceRepositoryDoctrine::class);
            $this->app->singleton(SyncLogRepositoryContract::class, SyncLogRepositoryDoctrine::class);
        }
    }

    /**
     *
     */
    private function bootCommands(): void
    {
        $this->commands(
            [
                SynchronizeCommand::class,
            ]
        );
    }

    /**
     *
     */
    private function bootSchedule(): void
    {
        $this->app->booted(
            function () {
                $schedule = app(Schedule::class);
                $schedule->command(SynchronizeCommand::SIGNATURE)->daily()->withoutOverlapping();
            }
        );
    }

    /**
     *
     */
    private function bootAliases(): void
    {
        $this->app->alias(
            ParticipantGoalSourceEntity::class,
            CRMImportableContract::CRM_ALIAS_PREFIX . CRMImportableContract::CRM_ENTITY_TYPE_PARTICIPANT_GOAL
        );
        $this->app->alias(
            ParticipantIEPSourceEntity::class,
            CRMImportableContract::CRM_ALIAS_PREFIX . CRMImportableContract::CRM_ENTITY_TYPE_PARTICIPANT_IEP
        );
        $this->app->alias(
            ParticipantSourceEntity::class,
            CRMImportableContract::CRM_ALIAS_PREFIX . CRMImportableContract::CRM_ENTITY_TYPE_PARTICIPANT
        );
        $this->app->alias(
            SchoolSourceEntity::class,
            CRMImportableContract::CRM_ALIAS_PREFIX . CRMImportableContract::CRM_ENTITY_TYPE_SCHOOL
        );
        $this->app->alias(
            SessionSourceEntity::class,
            CRMImportableContract::CRM_ALIAS_PREFIX . CRMImportableContract::CRM_ENTITY_TYPE_SESSION
        );
        $this->app->alias(
            TeamSourceEntity::class,
            CRMImportableContract::CRM_ALIAS_PREFIX . CRMImportableContract::CRM_ENTITY_TYPE_TEAM
        );
        $this->app->alias(
            ServiceSourceEntity::class,
            CRMImportableContract::CRM_ALIAS_PREFIX . CRMImportableContract::CRM_ENTITY_TYPE_SERVICE
        );
        $this->app->alias(
            ProviderSourceEntity::class,
            CRMImportableContract::CRM_ALIAS_PREFIX . CRMImportableContract::CRM_ENTITY_TYPE_PROVIDER
        );
    }
}
