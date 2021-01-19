<?php

namespace App\Assistants\CRM\Drivers\Therapylog;

use App\Assistants\CRM\Drivers\Contracts\CRMExporterInterface;
use App\Assistants\CRM\Drivers\DTO\Participant\Goal\GoalDTO;
use App\Assistants\CRM\Drivers\DTO\Participant\ParticipantDTO;
use App\Assistants\CRM\Drivers\DTO\Session\SessionDTO;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceTransaction;
use Arr;

/**
 * Class Exporter
 *
 * @package App\Assistants\CRM\Drivers\Therapylog
 */
class Exporter extends TherapylogClient implements CRMExporterInterface
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
    public function createSession(SessionDTO $dto): string
    {
        return (string)$this->createServiceTransaction(self::prepareSessionDTO($dto))->id();
    }

    /**
     * @inheritDoc
     */
    public function updateSession(SessionDTO $dto): string
    {
        return (string)$this->updateServiceTransaction((int)$dto->id, self::prepareSessionDTO($dto))->id();
    }

    /**
     * @param SessionDTO $dto
     *
     * @return array
     */
    private static function prepareSessionDTO(SessionDTO $dto): array
    {
        return [
            'service_transaction' => [
                'start_at' => $dto->scheduledOn,
                'end_at' => $dto->scheduledTo,
                'school_id' => (int)$dto->school->id,
                'service_type' => ServiceTransaction::SERVICE_TYPE_SCHEDULED,
                'document' => 0,
                'service_appointments_attributes' => [
                    [
                        'service_id' => $dto->type,
                        'student_appointments_attributes' => $dto->participants->map(
                            static function (ParticipantDTO $participant) {
                                return [
                                    'student_id' => (int)$participant->id,
                                    'student_appointment_goals_attributes' => $participant->goals->map(
                                        static function (GoalDTO $goal) {
                                            return [
                                                'progressable_id' => $goal->id,
                                                'activity' => $goal->name,
                                            ];
                                        }
                                    )->toArray(),
                                ];
                            }
                        )->toArray(),
                    ],
                ],
            ],
        ];
    }
}
