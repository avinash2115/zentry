<?php

namespace App\Components\CRM\Services;

use App\Assistants\CRM\Drivers\Contracts\CRMExporterInterface;
use App\Assistants\CRM\Drivers\Contracts\CRMImporterInterface;
use App\Assistants\CRM\Drivers\DTO\Participant\Goal\GoalDTO;
use App\Assistants\CRM\Drivers\DTO\Participant\IEP\IEPDTO;
use App\Assistants\CRM\Drivers\DTO\Participant\ParticipantDTO;
use App\Assistants\CRM\Drivers\DTO\Service\ServiceDTO;
use App\Assistants\CRM\Drivers\DTO\Provider\ProviderDTO;
use App\Assistants\CRM\Drivers\DTO\Session\SessionDTO;
use App\Assistants\CRM\Drivers\DTO\Team\School\SchoolDTO;
use App\Assistants\CRM\Drivers\DTO\Team\TeamDTO;
use App\Assistants\CRM\Services\Traits\CRMServiceTrait;
use App\Components\CRM\Contracts\CRMExportableContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\CRM\Services\Source\Traits\SourceServiceTrait;
use App\Components\CRM\Services\SyncLog\Traits\SyncLogServiceTrait;
use App\Components\CRM\Source\ParticipantGoalSourceEntity;
use App\Components\CRM\Source\ParticipantIEPSourceEntity;
use App\Components\CRM\Source\ParticipantSourceEntity;
use App\Components\CRM\Source\SchoolSourceEntity;
use App\Components\CRM\Source\ServiceSourceEntity;
use App\Components\CRM\Source\ProviderSourceEntity;
use App\Components\CRM\Source\SessionSourceEntity;
use App\Components\CRM\Source\SourceEntity;
use App\Components\CRM\Source\SourceReadonlyContract;
use App\Components\CRM\Source\TeamSourceEntity;
use App\Components\CRM\ValueObjects\Driver;
use App\Components\Services\Services\Traits\ServiceServiceTrait;
use App\Components\Provider\ProviderServices\Traits\ProviderServiceTrait;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Users\Participant\Goal\GoalReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Services\Auth\Traits\AuthServiceTrait;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Components\Users\Services\Team\Traits\TeamServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\Users\User\CRM\CRMContract;
use App\Components\Users\User\CRM\CRMReadonlyContract;
use App\Components\Users\User\CRM\Mutators\DTO\Mutator;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * Class CRMService
 *
 * @package App\Components\CRM\Services
 */
class CRMService implements CRMServiceContract
{
    use UserServiceTrait;
    use ServiceServiceTrait;
    use ProviderServiceTrait;
    use CRMServiceTrait;
    use TeamServiceTrait;
    use AuthServiceTrait;
    use ParticipantServiceTrait;
    use SessionServiceTrait;
    use SourceServiceTrait;
    use SyncLogServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var CRMReadonlyContract|null
     */
    private ?CRMReadonlyContract $crm = null;

    /**
     * @var UserReadonlyContract
     */
    private ?UserReadonlyContract $user = null;

    /**
     * @inheritDoc
     */
    public function workWithUserAndCRM(string $userId, string $crmId): CRMServiceContract
    {
        $userService = $this->userService__()->workWith($userId);

        return $this->setEntity($userService->readonly(), $userService->crmService()->workWith($crmId)->readonly());
    }

    /**
     * @return UserReadonlyContract
     * @throws PropertyNotInit
     */
    private function _user(): UserReadonlyContract
    {
        if (!$this->user instanceof UserReadonlyContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->user;
    }

    /**
     * @return CRMContract
     * @throws PropertyNotInit
     */
    private function _crm(): CRMContract
    {
        if (!$this->crm instanceof CRMContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->crm;
    }

    /**
     * @param UserReadonlyContract $user
     * @param CRMReadonlyContract  $crm
     *
     * @return CRMService
     */
    private function setEntity(UserReadonlyContract $user, CRMReadonlyContract $crm): CRMServiceContract
    {
        $this->user = $user;
        $this->crm = $crm;

        $this->sourceService__ = null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function drivers(): Collection
    {
        return collect(config('crm.crms.drivers'))->map(
            static function (array $values) {
                return new Driver(...array_values($values));
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function check(): CRMServiceContract
    {
        $this->configureImporterDriver()->check();

        return $this;
    }

    /**
     * @param string $type
     *
     * @return Collection|SourceReadonlyContract[]
     * @throws BindingResolutionException
     */
    private function fetchAllExistedSources(string $type): Collection
    {
        $this->sourceService__()->applyFilters(
            [
                'crm' => [
                    'id' => $this->_crm()->identity()->toString(),
                    'has' => true,
                ],
                'type' => [
                    'className' => $type,
                    'has' => true,
                ],
            ]
        );

        return $this->sourceService__()->listRO()->keyBy(fn(SourceReadonlyContract $source) => $source->sourceId());
    }

    /**
     * @param string     $crmEntityType
     * @param Collection $items
     * @param callable   $updateEntityCallback
     * @param callable   $createEntityCallback
     * @param callable   $removeEntityCallback
     *
     * @return void
     * @throws BindingResolutionException
     */
    private function _sync(
        string $crmEntityType,
        Collection $items,
        callable $updateEntityCallback,
        callable $createEntityCallback,
        ?callable $removeEntityCallback = null
    ): void {
        $sourceType = $this->sourceService__()->sourceEntityClass($crmEntityType);
        $existedSources = $this->fetchAllExistedSources($sourceType);

        $items->each(
            function ($itemDto) use (&$existedSources, $updateEntityCallback, $createEntityCallback) {
                if ($existedSources->has($itemDto->id())) {
                    $updateEntityCallback($existedSources->get($itemDto->id()), $itemDto);
                    $existedSources->forget($itemDto->id());
                } else {
                    $newEntity = $createEntityCallback($itemDto);

                    if ($newEntity instanceof CRMImportableContract) {
                        $this->sourceService__()->create(
                            $this->_crm(),
                            $newEntity,
                            [
                                'source_id' => $itemDto->id(),
                            ]
                        );
                    }
                }
            }
        );

        if (is_callable($removeEntityCallback)) {
            $existedSources->each(
                function (SourceReadonlyContract $source) use ($removeEntityCallback) {
                    $removeEntityCallback($source);
                    $this->sourceService__()->workWith($source->identity()->toString())->remove();
                }
            );
        }

        $this->syncLogService__()->create($this->_crm(), $crmEntityType);
    }

    /**
     * @inheritDoc
     */
    public function syncTeams(): void
    {
        $this->_sync(
            CRMImportableContract::CRM_ENTITY_TYPE_TEAM,
            $this->configureImporterDriver()->teams(),
            function (TeamSourceEntity $source, TeamDTO $team) {
                $this->teamService__()->workWith($source->owner()->identity()->toString())->change(
                    [
                        'name' => $team->name,
                    ]
                );
            },
            function (TeamDTO $team) {
                $this->authService__()->loginOnceFromUser($this->_user());

                return $this->teamService__()->create(['name' => $team->name])->readonly();
            },
            function (TeamSourceEntity $source) {
                $this->teamService__()->workWith(
                    $source->owner()->identity()->toString()
                )->remove();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function syncServices(): void
    {
        $this->_sync(
            CRMImportableContract::CRM_ENTITY_TYPE_SERVICE,
            $this->configureImporterDriver()->services(),
            function (ServiceSourceEntity $source, ServiceDTO $dto) {
                $this->serviceService__()->workWith($source->owner()->identity()->toString())->change(
                    [
                        'name' => $dto->name,
                        'code' => $dto->code,
                        'category' => $dto->category,
                        'status' => $dto->status,
                        'actions' => $dto->actions,


                    ]
                );
            },
            function (ServiceDTO $dto) {
                return $this->serviceService__()->create($this->_user(),
                 [
                    'name' => $dto->name,
                    'code' => $dto->code,
                    'category' => $dto->category,
                    'status' => $dto->status,
                    'actions' => $dto->actions,


                ])->readonly();
            },
            function (ServiceSourceEntity $source) {
                $this->serviceService__()->workWith(
                    $source->owner()->identity()->toString()
                )->remove();
            }
        );
    }

      /**
     * @inheritDoc
     */
    public function syncProviders(): void
    {
        $this->_sync(
            CRMImportableContract::CRM_ENTITY_TYPE_PROVIDER,
            $this->configureImporterDriver()->Providers(),
            function (ProviderSourceEntity $source, ProviderDTO $dto) {
                $this->providerService__()->workWith($source->owner()->identity()->toString())->change(
                    [
                        'name' => $dto->name,
                        'code' => $dto->code,
                

                    ]
                );
            },
            function (ProviderDTO $dto) {
                return $this->providerService__()->create($this->_user(),
                 [
                    'name' => $dto->name,
                    'code' => $dto->code,


                ])->readonly();
            },
            function (ProviderSourceEntity $source) {
                $this->providerService__()->workWith(
                    $source->owner()->identity()->toString()
                )->remove();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function syncParticipants(): void
    {
        $this->_sync(
            CRMImportableContract::CRM_ENTITY_TYPE_PARTICIPANT,
            $this->configureImporterDriver()->participants(),
            function (ParticipantSourceEntity $source, ParticipantDTO $participant) {
                $this->participantService__()->workWith($source->owner()->identity()->toString())->change(
                    [
                        'first_name' => $participant->firstName,
                        'last_name' => $participant->lastName,
                        'gender' => $participant->gender,
                        'dob' => $participant->birthDate,
                    ]
                );

                $this->participantService__()->therapyService()->change(
                    [
                        'frequency' => $participant->therapyTimePeriod,
                        'eligibility' => $participant->therapyEligibilityType,
                        'treatment_amount_planned' => $participant->therapyMinutes,
                    ]
                );
            },
            function (ParticipantDTO $participant) {
                try {
                    $teamSource = $this->findSourceBySourceId(TeamSourceEntity::class, $participant->districtId);
                } catch (NotFoundException $exception) {
                    $teamSource = null;
                }

                if ($teamSource === null) {
                    return null;
                }

                $this->participantService__()->create(
                    $this->_user(),
                    [
                        'first_name' => $participant->firstName,
                        'last_name' => $participant->lastName,
                        'gender' => $participant->gender,
                        'dob' => $participant->birthDate,
                    ],
                    $this->teamService__()->workWith(
                        $teamSource->owner()->identity()->toString()
                    )->readonly(),
                );

                $this->participantService__()->therapyService()->change(
                    [
                        'frequency' => $participant->therapyTimePeriod,
                        'eligibility' => $participant->therapyEligibilityType,
                        'treatment_amount_planned' => $participant->therapyMinutes,
                    ]
                );

                return $this->participantService__()->readonly();
            },
            function (ParticipantSourceEntity $source) {
                $this->participantService__()->workWith(
                    $source->owner()->identity()->toString()
                )->remove();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function syncParticipantsGoals(): void
    {
        $existedSources = $this->fetchAllExistedSources(ParticipantGoalSourceEntity::class);

        $this->fetchAllExistedSources(ParticipantSourceEntity::class)->each(
            function (ParticipantSourceEntity $source) use (&$existedSources) {
                $this->_sync(
                    CRMImportableContract::CRM_ENTITY_TYPE_PARTICIPANT_GOAL,
                    $this->configureImporterDriver()->participantsGoals($source->sourceId()),
                    function (ParticipantGoalSourceEntity $goalSource, GoalDTO $goal) use ($source, $existedSources) {
                        $this->participantService__()->workWith($source->owner()->identity())->goalService()->workWith(
                            $goalSource->owner()->identity()
                        )->change(
                            [
                                'name' => $goal->name,
                                'meta' => $goal->meta,
                            ]
                        );
                        $existedSources->forget($goal->id());
                    },
                    function (GoalDTO $goalDTO) use ($source) {
                        $this->participantService__()->workWith($source->owner()->identity())->goalService()->create(
                            [
                                'name' => $goalDTO->name,
                                'meta' => $goalDTO->meta,
                            ]
                        )->trackerService()->createDefault();

                        return $this->participantService__()->goalService()->readonly();
                    }
                );
            }
        );

        $this->participantService__()->applyFilters(
            [
                'goals' => [
                    'collection' => $existedSources->keys()->toArray(),
                ],
            ]
        );

        $this->participantService__()->listRO()->each(
            function (ParticipantReadonlyContract $participant) use ($existedSources) {
                $this->participantService__()->workWith($participant->identity());

                $participant->goals()->intersectByKeys($existedSources)->each(
                    function (GoalReadonlyContract $goal) {
                        $this->participantService__()->goalService()->workWith($goal->identity())->remove();
                    }
                );
            }
        );

        $existedSources->each(
            function (ParticipantGoalSourceEntity $source) {
                $this->sourceService__()->workWith($source->identity()->toString())->remove();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function syncParticipantsIEPs(): void
    {
        $existedSources = $this->fetchAllExistedSources(ParticipantIEPSourceEntity::class);

        $this->fetchAllExistedSources(ParticipantSourceEntity::class)->each(
            function (ParticipantSourceEntity $source) use (&$existedSources) {
                $this->_sync(
                    CRMImportableContract::CRM_ENTITY_TYPE_PARTICIPANT_IEP,
                    $this->configureImporterDriver()->participantsIEPs($source->sourceId()),

                    function (ParticipantIEPSourceEntity $iepSource, IEPDTO $iepDTO) use ($source, $existedSources) {
                        $this->participantService__()->workWith($source->owner()->identity())->IEPService()->workWith(
                            $iepSource->owner()->identity()
                        )->change(
                            [
                                'date_actual' => $iepDTO->effectiveOn,
                                'date_reeval' => $iepDTO->reevalDate,
                            ]
                        );

                        $iepDTO->goals->each(
                            function (GoalDTO $goalDTO) {
                                try {
                                    $this->participantService__()->goalService()->workWith(
                                        $this->findSourceBySourceId(ParticipantGoalSourceEntity::class, $goalDTO->id)
                                            ->owner()
                                            ->identity()
                                            ->toString()
                                    )->change(
                                        [
                                            'iep' => $this->participantService__()->IEPService()->readonly()->identity()->toString(),
                                        ]
                                    );
                                } catch (NotFoundException $exception) {
                                }
                            }
                        );

                        $existedSources->forget($iepDTO->id());
                    },
                    function (IEPDTO $iepDTO) use ($source) {
                        $this->participantService__()->workWith($source->owner()->identity())->IEPService()->create(
                            [
                                'date_actual' => $iepDTO->effectiveOn,
                                'date_reeval' => $iepDTO->reevalDate,
                            ]
                        );

                        $iepDTO->goals->each(
                            function (GoalDTO $goalDTO) {
                                try {
                                    $this->participantService__()->goalService()->workWith(
                                        $this->findSourceBySourceId(ParticipantGoalSourceEntity::class, $goalDTO->id)
                                            ->owner()
                                            ->identity()
                                            ->toString()
                                    )->change(
                                        [
                                            'iep' => $this->participantService__()->IEPService()->readonly()->identity()->toString()
                                        ]
                                    );
                                } catch (NotFoundException $exception) {
                                }
                            }
                        );

                        return $this->participantService__()->IEPService()->readonly();
                    }
                );
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function syncServiceTransactions(): void
    {
        $serviceTransactions = $this->configureImporterDriver()->serviceTransactions();

        $this->_syncSchools($serviceTransactions->schools());
        $this->_syncScheduledSessions($serviceTransactions->sessions());
    }

    /**
     * @inheritDoc
     */
    public function syncProviderTransactions(): void
    {
        $providerTransactions = $this->configureImporterDriver()->providerTransactions();

        $this->_syncSchools($providerTransactions->schools());
        $this->_syncScheduledSessions($providerTransactions->sessions());
    }


    /**
     * @inheritDoc
     */
    public function syncSchools(): void
    {
        $serviceTransactions = $this->configureImporterDriver()->serviceTransactions();

        $this->_syncSchools($serviceTransactions->schools());

        $providerTransactions = $this->configureImporterDriver()->providerTransactions();

        $this->_syncSchools($providerTransactions->schools());
    }

    /**
     * @inheritDoc
     */
    public function syncScheduledSessions(): void
    {
        $serviceTransactions = $this->configureImporterDriver()->serviceTransactions();

        $this->_syncScheduledSessions($serviceTransactions->sessions());

        $providerTransactions = $this->configureImporterDriver()->serviceTransactions();

        $this->_syncScheduledSessions($providerTransactions->sessions());
    }


    /**
     * @param Collection $schools
     *
     * @throws BindingResolutionException
     */
    private function _syncSchools(Collection $schools): void
    {
        $this->_sync(
            CRMImportableContract::CRM_ENTITY_TYPE_SCHOOL,
            $schools,
            function (SchoolSourceEntity $source, SchoolDTO $school) {
                $teamSource = $this->findSourceBySourceId(TeamSourceEntity::class, $school->districtId);

                $this->teamService__()
                    ->workWith($teamSource->owner()->identity()->toString())
                    ->schoolService()
                    ->workWith($source->owner()->identity()->toString())
                    ->change(
                        [
                            'name' => $school->name,
                            'available' => $school->available === 1,
                            'street_address' => $school->streetAddress,
                            'city' => $school->city,
                            'state' => $school->state,
                            'zip' => $school->zip,
                        ]
                    );
            },
            function (SchoolDTO $school) {
                $teamSource = $this->findSourceBySourceId(TeamSourceEntity::class, $school->districtId);

                return $this->teamService__()
                    ->workWith($teamSource->owner()->identity()->toString())
                    ->schoolService()
                    ->create(
                        [
                            'name' => $school->name,
                            'available' => $school->available === 1,
                            'street_address' => $school->streetAddress,
                            'city' => $school->city,
                            'state' => $school->state,
                            'zip' => $school->zip,
                        ]
                    )
                    ->readonly();
            },
            function (SchoolSourceEntity $source) {
                $this->teamService__()->applyFilters(
                    [
                        'schools' => [
                            'collection' => [$source->owner()->identity()->toString()],
                        ],
                    ]
                );

                $team = $this->teamService__()->listRO()->first();

                if ($team instanceof TeamReadonlyContract && $source->owner() instanceof SchoolReadonlyContract) {
                    $this->teamService__()->workWith($team->identity())->schoolService()->workWith(
                        $source->owner()->identity()->toString()
                    )->remove();
                }
            }
        );
    }

    /**
     * @param Collection $sessions
     *
     * @throws BindingResolutionException
     */
    private function _syncScheduledSessions(Collection $sessions): void
    {
        $participants = $this->fetchAllExistedSources(ParticipantSourceEntity::class);

        $sessions = $sessions->filter(
            static function (SessionDTO $dto) {
                return $dto->scheduledOn !== null;
            }
        );

        $this->_sync(
            CRMImportableContract::CRM_ENTITY_TYPE_SESSION,
            $sessions,
            function (SessionSourceEntity $source, SessionDTO $dto) {
                $this->sessionService__()->workWith(
                    $source->owner()->identity()->toString()
                )->change(
                    [
                        'scheduled_on' => $dto->scheduledOn,
                        'scheduled_to' => $dto->scheduledTo,
                    ]
                );

                $name = $this->sessionService__()->readonly()->participants()->map(
                    fn(ParticipantReadonlyContract $participant) => $participant->fullName()
                )->join(', ');

                try {
                    $service = $this->findSourceBySourceId(ServiceSourceEntity::class, $dto->service->id())->owner();
                } catch (NotFoundException $exception) {
                    $service = null;
                }

                try {
                    $school = $this->findSourceBySourceId(SchoolSourceEntity::class, $dto->school->id())->owner();
                } catch (NotFoundException $exception) {
                    $school = null;
                }

                if (!strEmpty($name)) {
                    $this->sessionService__()->change(
                        [
                            'name' => $name,
                            'service' => $service,
                            'school' => $school,
                        ]
                    );
                }
            },
            function (SessionDTO $dto) use ($participants) {
                try {
                    $service = $this->findSourceBySourceId(ServiceSourceEntity::class, $dto->service->id())->owner();
                } catch (NotFoundException $exception) {
                    $service = null;
                }

                try {
                    $school = $this->findSourceBySourceId(SchoolSourceEntity::class, $dto->school->id())->owner();
                } catch (NotFoundException $exception) {
                    $school = null;
                }

                $this->sessionService__()->create(
                    $this->_user(),
                    [
                        'name' => $dto->name,
                        'scheduled_on' => $dto->scheduledOn,
                        'scheduled_to' => $dto->scheduledTo,
                        'type' => $dto->type,
                        'school' => $school,
                        'service' => $service,
                    ]
                );

                $dto->participants->each(
                    function (ParticipantDTO $participantDTO) use ($participants) {
                        $existed = $participants->get($participantDTO->id);

                        if ($existed instanceof ParticipantSourceEntity) {
                            $this->sessionService__()->audienceService()->add(
                                $this->participantService__()->workWith($existed->owner()->identity())->readonly()
                            );
                        }
                    }
                );

                $name = $this->sessionService__()->readonly()->participants()->map(
                    fn(ParticipantReadonlyContract $participant) => $participant->fullName()
                )->join(', ');

                if (!strEmpty($name)) {
                    $this->sessionService__()->change(['name' => $name]);
                }

                return $this->sessionService__()->readonly();
            }
        );
    }

    /**
     * @return CRMImporterInterface
     * @throws BindingResolutionException
     */
    private function configureImporterDriver(): CRMImporterInterface
    {
        return $this->crmService__()->importer(
            $this->_crm()->driver(),
            $this->_crm()->config()->asTypeValueMap()->toArray()
        );
    }

    /**
     * @inheritDoc
     */
    public function export(CRMExportableContract $entity): void
    {
        switch (true) {
            case $entity instanceof SessionReadonlyContract:
                $this->exportSession($entity);
            break;

            default:
                throw new RuntimeException("Undefined export method");
        }
    }

    /**
     * @param SessionReadonlyContract $session
     *
     * @throws BindingResolutionException
     */
    private function exportSession(SessionReadonlyContract $session): void
    {
        $dto = new SessionDTO();

        $dto->scheduledOn = dateToISO8601(
            $session->scheduledOn() instanceof DateTime ? $session->scheduledOn() : $session->startedAt()
        );
        $dto->scheduledTo = dateToISO8601(
            $session->scheduledTo() instanceof DateTime ? $session->scheduledTo() : $session->endedAt()
        );
        $dto->type = $session->type();

        if ($session->school() instanceof SchoolReadonlyContract) {
            try {
                $school = new SchoolDTO();

                $school->id = $this->findSourceByOwnerId(
                    SchoolSourceEntity::class,
                    $session->school()->identity()
                )->sourceId();

                $dto->school = $school;
            } catch (NotFoundException $exception) {
                report($exception);

                return;
            }
        } else {
            return;
        }

        $dto->participants = $session->participants()->values()->map(
            function (ParticipantReadonlyContract $participant) {
                $participantDTO = new ParticipantDTO();

                $participantDTO->id = $this->findSourceByOwnerId(
                    ParticipantSourceEntity::class,
                    $participant->identity()
                )->sourceId();

                $participantDTO->goals = $participant->goals()->map(
                    static function (GoalReadonlyContract $goal) {
                        $goalDTO = new GoalDTO();

                        $goalDTO->name = $goal->name();

                        return $goalDTO;
                    }
                );

                return $participantDTO;
            }
        );

        try {
            $sessionSource = $this->findSourceByOwnerId(SessionSourceEntity::class, $session->identity());
        } catch (NotFoundException $exception) {
            $sessionSource = null;
        }

        if ($sessionSource instanceof SessionSourceEntity) {
            $dto->id = $sessionSource->sourceId();
            $this->configureExporterDriver()->updateSession($dto);
        } else {
            $newSessionId = $this->configureExporterDriver()->createSession($dto);
            $this->sourceService__()->create(
                $this->_crm(),
                $session,
                [
                    'source_id' => $newSessionId,
                    'direction' => SourceReadonlyContract::DIRECTION_OUT,
                ]
            );
        }
    }

    /**
     * @param string $type
     * @param string $ownerId
     *
     * @return SourceEntity
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    private function findSourceBySourceId(string $type, string $ownerId): SourceEntity
    {
        return $this->_findSource($type, 'sources', $ownerId);
    }

    /**
     * @param string $type
     * @param string $ownerId
     *
     * @return SourceEntity
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    private function findSourceByOwnerId(string $type, string $ownerId): SourceEntity
    {
        return $this->_findSource($type, 'owners', $ownerId);
    }

    /**
     * @param string $type
     * @param string $field
     * @param string $ownerId
     *
     * @return SourceEntity
     * @throws BindingResolutionException
     * @throws NotFoundException
     */
    private function _findSource(string $type, string $field, string $ownerId): SourceEntity
    {
        $this->sourceService__()->applyFilters(
            [
                'crm' => [
                    'id' => $this->_crm()->identity()->toString(),
                    'has' => true,
                ],
                'type' => [
                    'className' => $type,
                    'has' => true,
                ],
                $field => [
                    'collection' => [$ownerId],
                    'has' => true,
                ],
                'limit' => 1,
            ]
        );
        $source = $this->sourceService__()->listRO()->first();
        if (!$source instanceof SourceEntity) {
            throw new NotFoundException('Source not found');
        }

        return $source;
    }

    /**
     * @return CRMExporterInterface
     * @throws BindingResolutionException
     */
    private function configureExporterDriver(): CRMExporterInterface
    {
        return $this->crmService__()->exporter(
            $this->_crm()->driver(),
            $this->_crm()->config()->asTypeValueMap()->toArray()
        );
    }
}
