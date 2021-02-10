<?php

namespace App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ProviderTransaction;

use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\API\Entity;
use App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction\ServiceAppointment\ServiceAppointment;
use Arr;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class ProviderTransaction
 *
 * @package App\Assistants\CRM\Drivers\Therapylog\ValueObjects\ServiceTransaction
 */
class ProviderTransaction implements Entity
{
    public const STATUS_SCHEDULED = 'SCHEDULED';

    public const STATUS_INITIATED = 'INITIATED';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_APPROVED = 'APPROVED';

    public const STATUS_REASSIGNED = 'REASSIGNED';

    public const STATUS_REDOC = 'REDOC';

    public const SERVICE_TYPE_SCHEDULED = 'SCHEDULED';

    public const SERVICE_TYPE_UNSCHEDULED = 'UNSCHEDULED';

    /**
     * @var int
     */
    private int $id;

    /**
     * @var DateTime
     */
    private DateTime $startAt;

    /**
     * @var DateTime
     */
    private DateTime $endAt;

    /**
     * @var string
     */
    private string $status;

    /**
     * @var string
     */
    private string $serviceType;

    /**
     * @var DateTime
     */
    private DateTime $updatedAt;

    /**
     * @var School
     */
    private School $school;

    /**
     * @var Collection
     */
    private Collection $serviceAppointments;

    /**
     * ProviderTransaction constructor.
     *
     * @param array $args
     *
     * @throws Exception
     */
    public function __construct(array $args)
    {
        $this->setId((int)Arr::get($args, 'id', 0))->setStartAt(Arr::get($args, 'start_at', ''))->setEndAt(
                Arr::get($args, 'end_at', '')
            )->setStatus(Arr::get($args, 'status', ''))->setServiceType(Arr::get($args, 'service_type', ''))->setSchool(
                Arr::get($args, 'school', [])
            )->setServiceAppointments(Arr::get($args, 'service_appointments', []))->setUpdatedAt(Arr::get($args, 'updated_at', ''));
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return ProviderTransaction
     */
    private function setId(int $id): ProviderTransaction
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Id Should greater than zero.');
        }

        $this->id = $id;

        return $this;
    }

    /**
     * @return School
     */
    public function school(): School
    {
        return $this->school;
    }

    /**
     * @param array $data
     *
     * @return ProviderTransaction
     */
    private function setSchool(array $data): ProviderTransaction
    {
        $this->school = new School($data);

        return $this;
    }

    /**
     * @return DateTime
     */
    public function startAt(): DateTime
    {
        return $this->startAt;
    }

    /**
     * @param string $date
     *
     * @return ProviderTransaction
     * @throws Exception
     */
    private function setStartAt(string $date): ProviderTransaction
    {
        $this->startAt = new DateTime($date);

        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isOnSchedule(): bool
    {
        return new DateTime() < $this->startAt();
    }

    /**
     * @return DateTime
     */
    public function endAt(): DateTime
    {
        return $this->endAt;
    }

    /**
     * @param string $date
     *
     * @return ProviderTransaction
     * @throws Exception
     */
    private function setEndAt(string $date): ProviderTransaction
    {
        $this->endAt = new DateTime($date);

        return $this;
    }

    /**
     * @return DateTime
     */
    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param string $date
     *
     * @return ProviderTransaction
     * @throws Exception
     */
    private function setUpdatedAt(string $date): ProviderTransaction
    {
        $this->updatedAt = new DateTime($date);

        return $this;
    }

    /**
     * @return string
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return ProviderTransaction
     */
    private function setStatus(string $status): ProviderTransaction
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $status
     *
     * @return bool
     */
    public function isStatus(string $status): bool
    {
        return $this->status() === $status;
    }

    /**
     * @return bool
     */
    public function isScheduled(): bool
    {
        return $this->isStatus(self::STATUS_SCHEDULED);
    }

    /**
     * @return string
     */
    public function serviceType(): string
    {
        return $this->serviceType;
    }

    /**
     * @param string $serviceType
     *
     * @return ProviderTransaction
     */
    private function setServiceType(string $serviceType): ProviderTransaction
    {
        $this->serviceType = $serviceType;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return ProviderTransaction
     */
    private function setServiceAppointments(array $data): ProviderTransaction
    {
        $this->serviceAppointments = collect($data)->map(
            static function (array $data) {
                return new ServiceAppointment($data);
            }
        );

        return $this;
    }

    /**
     * @return Collection
     */
    public function serviceAppointments(): Collection
    {
        return $this->serviceAppointments;
    }
}
