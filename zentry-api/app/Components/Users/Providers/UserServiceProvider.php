<?php

namespace App\Components\Users\Providers;

use App;
use App\Components\Users\Console\DataProvider\Synchronize;
use App\Components\Users\Console\Elastic\Indexing\Participant\Index as ParticipantIndex;
use App\Components\Users\Console\CRM\ConnectionCheck;
use App\Components\Users\Console\Storage\Cloud\Quota;
use App\Components\Users\Device\DeviceContract;
use App\Components\Users\Device\DeviceEntity;
use App\Components\Users\Device\Repository\DeviceRepositoryContract;
use App\Components\Users\Device\Repository\DeviceRepositoryDoctrine;
use App\Components\Users\Device\Repository\DeviceRepositoryMemory;
use App\Components\Users\Login\Token\Repository\TokenRepositoryContract;
use App\Components\Users\Login\Token\Repository\TokenRepositoryDoctrine;
use App\Components\Users\Login\Token\Repository\TokenRepositoryMemory;
use App\Components\Users\Login\Token\TokenContract;
use App\Components\Users\Login\Token\TokenEntity;
use App\Components\Users\Participant\Goal\GoalContract;
use App\Components\Users\Participant\Goal\GoalEntity;
use App\Components\Users\Participant\IEP\IEPContract;
use App\Components\Users\Participant\IEP\IEPEntity;
use App\Components\Users\Participant\Goal\Tracker\TrackerContract;
use App\Components\Users\Participant\Goal\Tracker\TrackerEntity;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Participant\ParticipantEntity;
use App\Components\Users\Participant\Repository\ParticipantRepositoryContract;
use App\Components\Users\Participant\Repository\ParticipantRepositoryDoctrine;
use App\Components\Users\Participant\Repository\ParticipantRepositoryMemory;
use App\Components\Users\PasswordReset\PasswordResetContract;
use App\Components\Users\PasswordReset\PasswordResetEntity;
use App\Components\Users\PasswordReset\Repository\PasswordResetRepositoryContract;
use App\Components\Users\PasswordReset\Repository\PasswordResetRepositoryDoctrine;
use App\Components\Users\PasswordReset\Repository\PasswordResetRepositoryMemory;
use App\Components\Users\Services\Device\DeviceService;
use App\Components\Users\Services\Device\DeviceServiceContract;
use App\Components\Users\Services\Login\Token\TokenService;
use App\Components\Users\Services\Login\Token\TokenServiceContract;
use App\Components\Users\Services\Participant\Audience\AudienceService;
use App\Components\Users\Services\Participant\Audience\AudienceServiceContract;
use App\Components\Users\Services\Participant\Goal\GoalService;
use App\Components\Users\Services\Participant\Goal\GoalServiceContract;
use App\Components\Users\Services\Participant\Goal\Tracker\TrackerService;
use App\Components\Users\Services\Participant\Goal\Tracker\TrackerServiceContract;
use App\Components\Users\Services\Participant\IEP\IEPService;
use App\Components\Users\Services\Participant\IEP\IEPServiceContract;
use App\Components\Users\Services\Participant\ParticipantService;
use App\Components\Users\Services\Participant\ParticipantServiceContract;
use App\Components\Users\Services\PasswordReset\PasswordResetService;
use App\Components\Users\Services\PasswordReset\PasswordResetServiceContract;
use App\Components\Users\Services\User\CRM\CRMService;
use App\Components\Users\Services\User\CRM\CRMServiceContract;
use App\Components\Users\Services\Team\Request\RequestService;
use App\Components\Users\Services\Team\Request\RequestServiceContract;
use App\Components\Users\Services\Team\TeamService;
use App\Components\Users\Services\Team\TeamServiceContract;
use App\Components\Users\Services\User\DataProvider\DataProviderService;
use App\Components\Users\Services\User\DataProvider\DataProviderServiceContract;
use App\Components\Users\Services\Team\School\SchoolServiceContract;
use App\Components\Users\Services\Team\School\SchoolService;
use App\Components\Users\Services\User\Storage\StorageService;
use App\Components\Users\Services\User\Storage\StorageServiceContract;
use App\Components\Users\Services\User\UserService;
use App\Components\Users\Services\User\UserServiceContract;
use App\Components\Users\Team\Repository\TeamRepositoryContract;
use App\Components\Users\Team\Repository\TeamRepositoryDoctrine;
use App\Components\Users\Team\Repository\TeamRepositoryMemory;
use App\Components\Users\Team\Request\RequestContract;
use App\Components\Users\Team\Request\RequestEntity;
use App\Components\Users\Team\TeamContract;
use App\Components\Users\Team\TeamEntity;
use App\Components\Users\User\DataProvider\DataProviderContract;
use App\Components\Users\User\DataProvider\DataProviderEntity;
use App\Components\Users\Team\School\SchoolContract;
use App\Components\Users\Team\School\SchoolEntity;
use App\Components\Users\User\CRM\CRMContract;
use App\Components\Users\User\CRM\CRMEntity;
use App\Components\Users\User\Storage\StorageContract;
use App\Components\Users\User\Storage\StorageEntity;
use App\Components\Users\User\Poi\PoiContract;
use App\Components\Users\User\Poi\PoiEntity;
use App\Components\Users\User\Backtrack\BacktrackContract;
use App\Components\Users\User\Backtrack\BacktrackEntity;
use App\Components\Users\User\Profile\ProfileContract;
use App\Components\Users\User\Profile\ProfileEntity;
use App\Components\Users\User\Repository\UserRepositoryContract;
use App\Components\Users\User\Repository\UserRepositoryDoctrine;
use App\Components\Users\User\Repository\UserRepositoryMemory;
use App\Components\Users\User\UserContract;
use App\Components\Users\User\UserEntity;
use App\Components\Users\Participant\Therapy\TherapyContract;
use App\Components\Users\Participant\Therapy\TherapyEntity;
use App\Components\Users\Services\Participant\Therapy\TherapyServiceContract;
use App\Components\Users\Services\Participant\Therapy\TherapyService;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Providers\BaseServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

/**
 * Class UserServiceProvider
 */
class UserServiceProvider extends BaseServiceProvider
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
        $this->bootUsersConfigs();
        $this->bootCommands();
        $this->bootSchedule();
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
        $this->app->bind(PasswordResetContract::class, PasswordResetEntity::class);
        $this->app->bind(DeviceContract::class, DeviceEntity::class);
        $this->app->bind(TokenContract::class, TokenEntity::class);
        $this->app->bind(ParticipantContract::class, ParticipantEntity::class);
        $this->app->bind(TherapyContract::class, TherapyEntity::class);
        $this->app->bind(GoalContract::class, GoalEntity::class);
        $this->app->bind(IEPContract::class, IEPEntity::class);
        $this->app->bind(TrackerContract::class, TrackerEntity::class);

        $this->app->bind(TeamContract::class, TeamEntity::class);
        $this->app->bind(RequestContract::class, RequestEntity::class);

        $this->app->bind(UserContract::class, UserEntity::class);
        $this->app->bind(DataProviderContract::class, DataProviderEntity::class);
        $this->app->bind(ProfileContract::class, ProfileEntity::class);
        $this->app->bind(StorageContract::class, StorageEntity::class);
        $this->app->bind(PoiContract::class, PoiEntity::class);
        $this->app->bind(BacktrackContract::class, BacktrackEntity::class);
        $this->app->bind(CRMContract::class, CRMEntity::class);
        $this->app->bind(SchoolContract::class, SchoolEntity::class);
    }

    /**
     *
     */
    protected function registerServices(): void
    {
        $this->app->bind(UserServiceContract::class, UserService::class);
        $this->app->bind(DataProviderServiceContract::class, DataProviderService::class);
        $this->app->bind(StorageServiceContract::class, StorageService::class);
        $this->app->bind(PasswordResetServiceContract::class, PasswordResetService::class);
        $this->app->bind(DeviceServiceContract::class, DeviceService::class);
        $this->app->bind(TokenServiceContract::class, TokenService::class);
        $this->app->bind(CRMServiceContract::class, CRMService::class);

        $this->app->bind(TeamServiceContract::class, TeamService::class);
        $this->app->bind(RequestServiceContract::class, RequestService::class);

        $this->app->bind(ParticipantServiceContract::class, ParticipantService::class);
        $this->app->bind(TherapyServiceContract::class, TherapyService::class);
        $this->app->bind(GoalServiceContract::class, GoalService::class);
        $this->app->bind(IEPServiceContract::class, IEPService::class);
        $this->app->bind(TrackerServiceContract::class, TrackerService::class);
        $this->app->bind(AudienceServiceContract::class, AudienceService::class);

        $this->app->bind(SchoolServiceContract::class, SchoolService::class);
    }

    /**
     * Register repositories
     *
     * @return void
     */
    protected function registerRepositories(): void
    {
        if (App::runningUnitTests()) {
            $this->app->singleton(UserRepositoryContract::class, UserRepositoryMemory::class);
            $this->app->singleton(PasswordResetRepositoryContract::class, PasswordResetRepositoryMemory::class);
            $this->app->singleton(DeviceRepositoryContract::class, DeviceRepositoryMemory::class);
            $this->app->singleton(TokenRepositoryContract::class, TokenRepositoryMemory::class);
            $this->app->singleton(ParticipantRepositoryContract::class, ParticipantRepositoryMemory::class);
            $this->app->singleton(TeamRepositoryContract::class, TeamRepositoryMemory::class);
        } else {
            $this->app->singleton(UserRepositoryContract::class, UserRepositoryDoctrine::class);
            $this->app->singleton(PasswordResetRepositoryContract::class, PasswordResetRepositoryDoctrine::class);
            $this->app->singleton(DeviceRepositoryContract::class, DeviceRepositoryDoctrine::class);
            $this->app->singleton(TokenRepositoryContract::class, TokenRepositoryDoctrine::class);
            $this->app->singleton(ParticipantRepositoryContract::class, ParticipantRepositoryDoctrine::class);
            $this->app->singleton(TeamRepositoryContract::class, TeamRepositoryDoctrine::class);
        }
    }

    /**
     *
     */
    private function bootCommands(): void
    {
        $this->commands(
            [
                Quota::class,
                ParticipantIndex::class,
                Synchronize::class,
                ConnectionCheck::class,
            ]
        );
    }

    /**
     *
     */
    private function bootSchedule(): void
    {
        $this->app->booted(function () {
            $schedule = app(Schedule::class);
            $schedule->command(Quota::SIGNATURE)->daily()->withoutOverlapping();
            $schedule->command(ConnectionCheck::SIGNATURE)->everyTenMinutes()->withoutOverlapping();
            $schedule->command(Synchronize::SIGNATURE)->everyTenMinutes()->withoutOverlapping();
        });
    }

    /**
     *
     */
    protected function bootUsersConfigs(): void
    {
        parent::bootConfigs('users');
    }

}
