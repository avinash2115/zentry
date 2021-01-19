<?php

namespace App\Components\Sessions\Session;

use App\Components\CRM\Contracts\CRMExportableContract;
use App\Components\CRM\Contracts\CRMImportableContract;
use App\Components\Services\Service\ServiceReadonlyContract;
use App\Components\Sessions\ValueObjects\Geo;
use App\Components\Users\Participant\Contracts\AudiencableReadonlyContract;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Entities\Contracts\DirtiableContract;
use App\Convention\Entities\Contracts\IdentifiableContract;
use App\Convention\Entities\Contracts\TimestampableContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Source;
use App\Convention\ValueObjects\Tags;
use DateTime;
use Illuminate\Support\Collection;

/**
 * Interface SessionReadonlyContract
 *
 * @package App\Components\Sessions\Session
 */
interface SessionReadonlyContract extends IdentifiableContract, TimestampableContract, DirtiableContract, AudiencableReadonlyContract, CRMImportableContract, CRMExportableContract
{
    public const TYPE_DEFAULT = 'default';

    public const AVAILABLE_TYPES = [
        self::TYPE_DEFAULT,
    ];

    public const STATUS_NEW = 0;

    public const STATUS_STARTED = 10;

    public const STATUS_ENDED = 20;

    public const STATUS_WRAPPED = 30;

    public const AVAILABLE_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_STARTED,
        self::STATUS_ENDED,
        self::STATUS_WRAPPED,
    ];

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return int
     */
    public function status(): int;

    /**
     * @param int $status
     *
     * @return bool
     */
    public function isStatus(int $status): bool;

    /**
     * @return bool
     */
    public function isStarted(): bool;

    /**
     * @return bool
     */
    public function isEnded(): bool;

    /**
     * @return bool
     */
    public function isWrapped(): bool;

    /**
     * @return bool
     */
    public function isDead(): bool;

    /**
     * @return string
     */
    public function description(): string;

    /**
     * @return Geo|null
     */
    public function geo(): ?Geo;

    /**
     * @return Tags
     */
    public function tags(): Tags;

    /**
     * @return string|null
     */
    public function thumbnail(): ?string;

    /**
     * @return DateTime|null
     */
    public function startedAt(): ?DateTime;

    /**
     * @return DateTime|null
     */
    public function endedAt(): ?DateTime;

    /**
     * @return DateTime|null
     */
    public function scheduledOn(): ?DateTime;

    /**
     * @return DateTime|null
     */
    public function scheduledTo(): ?DateTime;

    /**
     * @return bool
     */
    public function isScheduled(): bool;

    /**
     * @return string|null
     */
    public function sign(): ?string;

    /**
     * @return array
     */
    public function excludedGoals(): array;

    /**
     * @return UserReadonlyContract
     */
    public function user(): UserReadonlyContract;

    /**
     * @return ServiceReadonlyContract|null
     */
    public function service(): ?ServiceReadonlyContract;

    /**
     * @return SchoolReadonlyContract|null
     */
    public function school(): ?SchoolReadonlyContract;

    /**
     * @return Collection
     */
    public function pois(): Collection;

    /**
     * @return Collection
     */
    public function streams(): Collection;

    /**
     * @param Collection $types
     *
     * @return Collection
     * @throws NotFoundException
     */
    public function streamsByTypes(Collection $types): Collection;

    /**
     * @return Collection
     */
    public function notes(): Collection;

    /**
     * @return string|null
     */
    public function reference(): ?string;

    /**
     * @return Collection
     */
    public function soaps(): Collection;

    /**
     * @return Collection
     */
    public function progress(): Collection;

    /**
     * @return Collection
     */
    public function goals(): Collection;
}
