<?php

namespace App\Components\Users\Services\User\CRM;

use App\Assistants\Events\EventRegistry;
use App\Components\CRM\Contracts\CRMExportableContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\CRM\Jobs\Synchronize;
use App\Components\CRM\Services\SyncLog\Traits\SyncLogServiceTrait;
use App\Components\CRM\Services\Traits\CRMServiceTrait;
use App\Components\Users\User\CRM\CRMContract;
use App\Components\Users\User\CRM\CRMDTO;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\CRM\Mutators\DTO\Mutator;
use App\Components\Users\User\Events\CRM\Connection\Lost;
use App\Components\Users\User\UserContract;
use App\Components\Users\ValueObjects\CRM\Config\Config;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Jobs\QueuesConstants;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Flusher;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class CRMService
 *
 * @package App\Components\Users\Services\User\CRM
 */
class CRMService implements CRMServiceContract
{
    use CRMServiceTrait;
    use SyncLogServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var CRMContract|null
     */
    private ?CRMContract $entity = null;

    /**
     * @var UserContract
     */
    private UserContract $user;

    /**
     * CRMService constructor.
     *
     * @param UserContract $user
     */
    public function __construct(UserContract $user)
    {
        $this->user = $user;
    }

    /**
     * @return Mutator
     * @throws BindingResolutionException
     */
    private function _mutator(): Mutator
    {
        $this->setMutator();

        return $this->mutator;
    }

    /**
     * @return self
     * @throws BindingResolutionException
     */
    private function setMutator(): self
    {
        if (!$this->mutator instanceof Mutator) {
            $this->mutator = app()->make(Mutator::class);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): CRMServiceContract
    {
        return $this->setEntity($this->_user()->crmByIdentity(new Identity($id)));
    }

    /**
     * @inheritDoc
     */
    public function workWithDriver(string $driver): CRMServiceContract
    {
        return $this->setEntity($this->_user()->crmByDriver($driver));
    }

    /**
     * @return CRMContract
     * @throws PropertyNotInit
     */
    private function _entity(): CRMContract
    {
        if (!$this->entity instanceof CRMContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param CRMContract $entity
     *
     * @return CRMService
     */
    private function setEntity(CRMContract $entity): CRMServiceContract
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function identity(): Identity
    {
        return $this->_entity()->identity();
    }

    /**
     * @inheritDoc
     */
    public function readonly(): CRMReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): CRMDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (CRMReadonlyContract $crm) {
                return $this->_mutator()->toDTO($crm);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_user()->crms();
    }

    /**
     * @inheritDoc
     */
    public function connect(array $data): CRMServiceContract
    {
        $entity = $this->make($data);

        $this->setEntity($entity);

        $this->_entity()->enable();

        $this->_user()->connectCRM($entity);

        if (!app()->runningInConsole()) {
            $this->check();
        }

        if (!app()->runningUnitTests()) {
            dispatch(new Synchronize($this->_user()->identity(), $this->_entity()->identity()))->onQueue(
                QueuesConstants::QUEUE_CRM_SYNC
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): CRMServiceContract
    {
        if (Arr::has($data, 'active')) {
            $active = filter_var(Arr::get($data, 'active'), FILTER_VALIDATE_BOOLEAN);

            if ($active && !$this->_entity()->active()) {
                $this->_entity()->enable();
                $this->_entity()->clearNotified();
            } elseif (!$active && $this->_entity()->active()) {
                $this->_entity()->disable()->markNotified();
                app()->make(EventRegistry::class)->register(new Lost($this->_user(), $this->readonly()));
            }
        }

        if (Arr::has($data, 'config')) {
            $driverConfig = $this->drivers()->get($this->_entity()->driver())->config();

            $config = $this->makeConfig(
                $this->_entity()->driver(),
                collect(Arr::get($data, 'config', []))->map(
                    static function (string $value, string $attribute) use ($driverConfig) {
                        return [
                            'type' => $attribute,
                            'value' => $value,
                            'encryption' => $driverConfig[$attribute]['encryption'],
                        ];
                    }
                )->values()->toArray()
            );

            $this->_entity()->changeConfig($config);
            $this->check();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function disconnect(): CRMServiceContract
    {
        $this->_user()->connectCRM($this->_entity());

        $this->entity = null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function drivers(): Collection
    {
        return $this->crmService__()->drivers();
    }

    /**
     * @param array $data
     *
     * @return CRMContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    private function make(array $data): CRMContract
    {
        $driver = Arr::get($data, 'driver', '');

        if (!$this->drivers()->has($driver)) {
            throw new InvalidArgumentException("Undefined driver: {$driver}");
        }

        $driverConfig = $this->drivers()->get($driver)->config();

        return app()->make(
            CRMContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $this->_user(),
                'driver' => $driver,
                'config' => $this->makeConfig(
                    $driver,
                    collect(Arr::get($data, 'config', []))->map(
                        static function (string $value, string $attribute) use ($driverConfig) {
                            return [
                                'type' => $attribute,
                                'value' => $value,
                                'encryption' => $driverConfig[$attribute]['encryption'],
                            ];
                        }
                    )->values()->toArray()
                ),
            ]
        );
    }

    /**
     * @param string $driver
     * @param array  $options
     *
     * @return Config
     */
    private function makeConfig(string $driver, array $options): Config
    {
        return new Config($options, $driver);
    }

    /**
     * @return UserContract
     * @throws PropertyNotInit
     */
    private function _user(): UserContract
    {
        if (!$this->user instanceof UserContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function check(): CRMServiceContract
    {
        $this->crmService__()->workWithUserAndCRM(
            $this->_user()->identity()->toString(),
            $this->_entity()->identity()->toString()
        )->check();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function sync(?string $type = null): CRMServiceContract
    {
        $service = $this->crmService__()->workWithUserAndCRM(
            $this->_user()->identity()->toString(),
            $this->_entity()->identity()->toString()
        );

        switch ($type) {
            case CRMImportableContract::CRM_ENTITY_TYPE_TEAM:
                $service->syncTeams();
                Flusher::flush();
            break;

            case CRMImportableContract::CRM_ENTITY_TYPE_SERVICE:
                $service->syncServices();
                Flusher::flush();
            break;

            case CRMImportableContract::CRM_ENTITY_TYPE_PARTICIPANT:
                $service->syncParticipants();
                Flusher::flush();
                $service->syncParticipantsGoals();
                Flusher::flush();
                $service->syncParticipantsIEPs();
                Flusher::flush();
            break;

            case CRMImportableContract::CRM_ENTITY_TYPE_SCHOOL:
                $service->syncSchools();
                Flusher::flush();
            break;

            case CRMImportableContract::CRM_ENTITY_TYPE_SESSION:
                $service->syncScheduledSessions();
                Flusher::flush();
            break;

            case null:
                $service->syncTeams();
                Flusher::flush();
                $service->syncServices();
                Flusher::flush();
                $service->syncParticipants();
                Flusher::flush();
                $service->syncParticipantsGoals();
                Flusher::flush();
                $service->syncParticipantsIEPs();
                Flusher::flush();
                $service->syncServiceTransactions();
                Flusher::flush();
            break;

            default:
                throw new InvalidArgumentException("Undefined source entity type: {$type}");
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function export(CRMExportableContract $entity): CRMServiceContract
    {
        $this->crmService__()->workWithUserAndCRM(
            $this->_user()->identity()->toString(),
            $this->_entity()->identity()->toString()
        )->export($entity);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function lastLog(string $type): Collection
    {
        $this->syncLogService__()->applyFilters(
            [
                'crm' => [
                    'id' => $this->_entity()->identity()->toString(),
                ],
                'types' => [
                    'collection' => [$type],
                    'has' => true,
                ],
                'limit' => 1,
                'order' => [
                    '-created_at',
                ],
            ]
        );

        return $this->syncLogService__()->list();
    }
}
