<?php

namespace App\Assistants\CRM\Drivers\Therapylog;

use App\Assistants\CRM\Drivers\Contracts\CRMImporterInterface;
use App\Assistants\CRM\Drivers\DTO\Participant\Goal\GoalDTO;
use App\Assistants\CRM\Drivers\DTO\Participant\IEP\IEPDTO;
use App\Assistants\CRM\Drivers\DTO\Participant\ParticipantDTO;
use App\Assistants\CRM\Drivers\DTO\Service\ServiceDTO;
use App\Assistants\CRM\Drivers\DTO\Provider\ProviderDTO;
use App\Assistants\CRM\Drivers\DTO\Session\SessionDTO;
use App\Assistants\CRM\Drivers\DTO\Team\School\SchoolDTO;
use App\Assistants\CRM\Drivers\DTO\Team\TeamDTO;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Records;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Caseload\Caseload;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Caseload\Student;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\District;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Goal\Goal;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\IEP\IEP;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Service;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\Providers;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment\ServiceAppointment;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment\StudentAppointment\StudentAppointment;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceTransaction;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ProviderTransaction;
use App\Assistants\CRM\Drivers\ValueObjects\Converted\ServiceTransaction as ConvertedServiceTransaction;
use App\Assistants\CRM\Drivers\ValueObjects\Converted\ProviderTransaction as ConvertedProviderTransaction;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Participant\Therapy\TherapyReadonlyContract;
use Arr;
use Cache;
use DateInterval;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use RuntimeException;
use Log;

/**
 * Class Importer
 *
 * @package App\Assistants\CRM\Drivers\Therapylog
 */
class Importer extends TherapylogClient implements CRMImporterInterface
{
    /**
     * Adapter constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct(Arr::get($config, 'email'), Arr::get($config, 'password'));
    }

    /**
     * @inheritDoc
     */
    public function check(): void
    {
        $this->login();
    }

    /**
     * @inheritDoc
     */
    public function teams(): Collection
    {
        return $this->provider()->districts()->map(
            static function (District $district) {
                $dto = new TeamDTO();
                $dto->id = (string)$district->id();
                $dto->name = $district->name();

                return $dto;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function services(): Collection
    {
        return parent::services()->map(
            static function (Service $service) {
                $dto = new ServiceDTO();
                $dto->id = (string)$service->id();
                $dto->name = $service->name();
                $dto->code = $service->code();
                $dto->category = $service->category();
                $dto->status = $service->status();
                $dto->actions = $service->actions();


                return $dto;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function providers(): Collection
    {
        return parent::providers()->map(
            static function (provider $provider) {
                $dto = new ProviderDTO();
                $dto->id = (string)$provider->id();
                $dto->name = $provider->name();
                $dto->code = $provider->code();
               

                return $dto;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function participants(): Collection
    {
        $caseloads = collect([]);
        $students = collect([]);
        $this->allEntities('caseloadRecords', ['districtId' => null, 'page' => 1], $caseloads);
        $caseloads->map(
            function ($caseload) use ($students) {
                if (!$caseload instanceof Caseload) {
                    throw new RuntimeException('Undefined entity "Caseload"');
                }

                $caseload->students()->each(
                    function (Student $student) use ($caseload, $students) {
                        if (!$students->has($student->id())) {
                            $dto = new ParticipantDTO();
                            $dto->id = (string)$student->id();
                            $dto->firstName = $student->firstName();
                            $dto->lastName = $student->lastName();
                            $dto->birthDate = dateTimeFormatted(new DateTime($student->birthDate()));
                            $dto->gender = $student->isGender(
                                Student::GENDER_MALE
                            ) ? ParticipantReadonlyContract::GENDER_MALE : ParticipantReadonlyContract::GENDER_FEMALE;

                            $dto->districtId = (string)$student->districtId();

                            $dto->therapyMinutes = $caseload->minutes();
                            $dto->therapyTimePeriod = str_replace(
                                [
                                    Caseload::TIME_PERIOD_DAY,
                                    Caseload::TIME_PERIOD_WEEK,
                                    Caseload::TIME_PERIOD_BIWEEK,
                                    Caseload::TIME_PERIOD_MONTH,
                                    Caseload::TIME_PERIOD_TRIMESTER,
                                ],
                                [
                                    TherapyReadonlyContract::FREQUENCY_DAILY,
                                    TherapyReadonlyContract::FREQUENCY_WEEKLY,
                                    TherapyReadonlyContract::FREQUENCY_BIWEEKLY,
                                    TherapyReadonlyContract::FREQUENCY_MONTHLY,
                                    TherapyReadonlyContract::FREQUENCY_TRIMESTER,
                                ],
                                $caseload->timePeriod()
                            );
                            $dto->therapyEligibilityType = str_replace(
                                [
                                    Caseload::ELIGIBILITY_TYPE_TODAY,
                                    Caseload::ELIGIBILITY_TYPE_ONE_TIME,
                                    Caseload::ELIGIBILITY_TYPE_ANNUAL,
                                    Caseload::ELIGIBILITY_TYPE_DENIED,
                                    Caseload::ELIGIBILITY_TYPE_INELIGIBLE,
                                ],
                                [
                                    TherapyReadonlyContract::ELIGIBILITY_TYPE_TODAY,
                                    TherapyReadonlyContract::ELIGIBILITY_TYPE_ONE_TIME,
                                    TherapyReadonlyContract::ELIGIBILITY_TYPE_ANNUAL,
                                    TherapyReadonlyContract::ELIGIBILITY_TYPE_DENIED,
                                    TherapyReadonlyContract::ELIGIBILITY_TYPE_INELIGIBLE,
                                ],
                                $caseload->eligibilityType()
                            );
                            $dto->goals = collect();

                            $students->put($student->id(), $dto);
                        }
                    }
                );
            }
        );

        return $students;
    }

    /**
     * @inheritDoc
     */
    public function participantsGoals(string $participantId): Collection
    {
        return $this->goals((int)$participantId)->goals()->map(
            static function (Goal $goal) {
                $dto = new GoalDTO();
                $dto->id = (string)$goal->id();
                $dto->name = $goal->name();
                $dto->meta = $goal->raw();

                return $dto;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function participantsIEPs(string $participantId): Collection
    {
        return $this->ieps((int)$participantId)->ieps()->map(
            static function (IEP $iep) {
                $dto = new IEPDTO();
                $dto->id = (string)$iep->id();
                $dto->effectiveOn = $iep->effectiveOn();
                $dto->reevalDate = $iep->reevalDate();
                $dto->goals = $iep->goals()->goals()->map(
                    static function (Goal $goal) {
                        $dto = new GoalDTO();
                        $dto->id = (string)$goal->id();
                        $dto->name = $goal->name();
                        $dto->meta = $goal->raw();

                        return $dto;
                    }
                );

                $dto->meta = $iep->raw();

                return $dto;
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function serviceTransactions(): ConvertedServiceTransaction
    {
        $key = $this->email() . ':serviceTransactionFromDate';

        if (app()->environment('local')) {
            Cache::forget($key);
        }

        $dateFrom = Cache::get($key, toUTC(new DateTime('@-0')))->setTime(0, 0, 0);
        $serviceTransactions = collect();
        $schools = collect();
        $scheduledSessions = collect();

        $this->allEntities(
            'serviceTransactionRecords',
            [
                'fromDate' => dateToISO8601($dateFrom),
                'toDate' => dateToISO8601(toUTC((new DateTime('now'))->add(new DateInterval("P1Y")))),
                'page' => 1,
            ],
            $serviceTransactions
        );

        $serviceTransactions->map(
            static function (ServiceTransaction $serviceTransaction) use (
                $schools,
                $scheduledSessions,
                $dateFrom,
                $key
            ) {
                if ($serviceTransaction->updatedAt()->getTimestamp() > $dateFrom->getTimestamp()) {
                    $dateFrom = $serviceTransaction->updatedAt();
                    Cache::put($key, $dateFrom);
                }

                $school = $serviceTransaction->school();

                $schoolDTO = new SchoolDTO();
                $schoolDTO->id = (string)$school->id();
                $schoolDTO->name = $school->name();
                $schoolDTO->districtId = (string)$school->districtId();
                $schoolDTO->available = (int)$school->isActive();
                $schoolDTO->streetAddress = $school->streetAddress();
                $schoolDTO->city = $school->city();
                $schoolDTO->state = $school->state();
                $schoolDTO->zip = $school->zip();

                if ($school->districtId() > 0 && !$schools->has($school->id())) {
                    $schools->put($school->id(), $schoolDTO);
                }

                if ($serviceTransaction->isScheduled()) {
                    $dto = new SessionDTO();
                    $dto->id = (string)$serviceTransaction->id();
                    $dto->name = $serviceTransaction->startAt()->format('F m Y H:i A');
                    $dto->scheduledOn = dateTimeFormatted(toUTC($serviceTransaction->startAt()));
                    $dto->scheduledTo = dateTimeFormatted(toUTC($serviceTransaction->endAt()));
                    $dto->school = $schoolDTO;

                    $dto->participants = collect();

                    $serviceTransaction->serviceAppointments()->each(
                        static function (ServiceAppointment $serviceAppointment) use ($dto) {
                            $dto->type = SessionReadonlyContract::TYPE_DEFAULT;

                            $serviceDTO = new ServiceDTO();
                            $serviceDTO->id = (string)$serviceAppointment->service()->id();
                            $serviceDTO->name = $serviceAppointment->service()->name();

                            $dto->service = $serviceDTO;

                            $serviceAppointment->studentAppointments()->each(
                                static function (StudentAppointment $studentAppointment) use ($dto) {
                                    $participantDTO = new ParticipantDTO();
                                    $participantDTO->id = (string)$studentAppointment->student()->id();
                                    $dto->participants->push($participantDTO);
                                }
                            );
                        }
                    );

                    $scheduledSessions->put($dto->id, $dto);
                }
            }
        );

        return new ConvertedServiceTransaction($schools->values(), $scheduledSessions->values());
    }

        /**
     * @inheritDoc
     */
    public function providerTransactions(): ConvertedServiceTransaction
    {
        $key = $this->email() . ':providerTransactionFromDate';

        if (app()->environment('local')) {
            Cache::forget($key);
        }

        $dateFrom = Cache::get($key, toUTC(new DateTime('@-0')))->setTime(0, 0, 0);
        $providerTransactions = collect();
        $schools = collect();
        $scheduledSessions = collect();

        $this->allEntities(
            'providerTransactionRecords',
            [
                'fromDate' => dateToISO8601($dateFrom),
                'toDate' => dateToISO8601(toUTC((new DateTime('now'))->add(new DateInterval("P1Y")))),
                'page' => 1,
            ],
            $providerTransactions
        );

        $providerTransactions->map(
            static function (ProviderTransaction $providerTransaction) use (
                $schools,
                $scheduledSessions,
                $dateFrom,
                $key
            ) {
                if ($providerTransaction->updatedAt()->getTimestamp() > $dateFrom->getTimestamp()) {
                    $dateFrom = $providerTransaction->updatedAt();
                    Cache::put($key, $dateFrom);
                }

                $school = $providerTransaction->school();

                $schoolDTO = new SchoolDTO();
                $schoolDTO->id = (string)$school->id();
                $schoolDTO->name = $school->name();
                $schoolDTO->districtId = (string)$school->districtId();
                $schoolDTO->available = (int)$school->isActive();
                $schoolDTO->streetAddress = $school->streetAddress();
                $schoolDTO->city = $school->city();
                $schoolDTO->state = $school->state();
                $schoolDTO->zip = $school->zip();

                if ($school->districtId() > 0 && !$schools->has($school->id())) {
                    $schools->put($school->id(), $schoolDTO);
                }

                if ($providerTransaction->isScheduled()) {
                    $dto = new SessionDTO();
                    $dto->id = (string)$providerTransaction->id();
                    $dto->name = $providerTransaction->startAt()->format('F m Y H:i A');
                    $dto->scheduledOn = dateTimeFormatted(toUTC($providerTransaction->startAt()));
                    $dto->scheduledTo = dateTimeFormatted(toUTC($providerTransaction->endAt()));
                    $dto->school = $schoolDTO;

                    $dto->participants = collect();

                    $providerTransaction->serviceAppointments()->each(
                        static function (ServiceAppointment $serviceAppointment) use ($dto) {
                            $dto->type = SessionReadonlyContract::TYPE_DEFAULT;

                            $serviceDTO = new ServiceDTO();
                            $serviceDTO->id = (string)$serviceAppointment->service()->id();
                            $serviceDTO->name = $serviceAppointment->service()->name();

                            $dto->service = $serviceDTO;

                            $serviceAppointment->studentAppointments()->each(
                                static function (StudentAppointment $studentAppointment) use ($dto) {
                                    $participantDTO = new ParticipantDTO();
                                    $participantDTO->id = (string)$studentAppointment->student()->id();
                                    $dto->participants->push($participantDTO);
                                }
                            );
                        }
                    );

                    $scheduledSessions->put($dto->id, $dto);
                }
            }
        );

        return new ConvertedProviderTransaction($schools->values(), $scheduledSessions->values());
    }

    /**
     * @param string     $method
     * @param array      $params
     * @param Collection $collection
     *
     * @throws BindingResolutionException
     */
    private function allEntities(string $method, array $params, Collection $collection): void
    {
        $page = Arr::get($params, 'page');
        $result = $this->executeMethod($method, $params);

        if (!$result instanceof Records) {
            throw new RuntimeException('Undefined entity "Records"');
        }

        $result->entities()->map(fn($entity) => $collection->push($entity));

        if ($result->meta()->nextPage() >= $page + 1) {
            $this->allEntities($method, Arr::set($params, 'page', $page + 1), $collection);
        }
    }

    /**
     * This method is for phpstan correct work
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    private function executeMethod(string $method, array $params = [])
    {
        $self = $this;
        if (method_exists($self, $method)) {
            $callback = function (...$parameters) use (
                $self,
                $method
            ) {
                return $self->$method(...$parameters);
            };

            return call_user_func_array(
                $callback,
                $params
            );
        }

        throw new RuntimeException("Method not found: {$method}");
    }
}
