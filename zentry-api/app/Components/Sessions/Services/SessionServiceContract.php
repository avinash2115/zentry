<?php

namespace App\Components\Sessions\Services;

use App\Assistants\Elastic\Contracts\Indexable\IndexableContract;
use App\Assistants\Events\BroadcastEventAbstract;
use App\Assistants\Files\Services\Contracts\HasFiles;
use App\Assistants\QR\Contracts\PayloadProvider;
use App\Components\Sessions\Services\Goal\GoalServiceContract;
use App\Assistants\Search\Agency\Services\Searchable;
use App\Components\Sessions\Services\Note\NoteServiceContract;
use App\Components\Sessions\Services\Poi\PoiServiceContract;
use App\Components\Sessions\Services\Progress\ProgressServiceContract;
use App\Components\Sessions\Services\SOAP\SOAPServiceContract;
use App\Components\Sessions\Services\Stream\StreamServiceContract;
use App\Components\Sessions\Session\SessionDTO;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Share\Contracts\SharableContract;
use App\Components\Users\Services\Participant\Audience\AudiencableServiceContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Services\Contracts\FilterableContract;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface SessionServiceContract
 *
 * @package App\Components\Sessions\Services\Session
 */
interface SessionServiceContract extends FilterableContract, PayloadProvider, HasFiles, AudiencableServiceContract, SharableContract, IndexableContract, Searchable
{
    public const ROUTE_CONNECT_DEVICE = 'sessions.devices.connect';

    public const BROADCAST_CHANNEL_EXACT = self::BROADCAST_CHANNEL_BASE . '-' . self::BROADCAST_CHANNEL_PARAMETER;

    public const BROADCAST_CHANNEL_BASE = BroadcastEventAbstract::USER_CHANNEL_BASE . '.sessions';

    public const BROADCAST_CHANNEL_PARAMETER = '{sessionId}';

    public const SAVE_DEVICE_TTL = 60;

    /**
     * @param string $id
     *
     * @return SessionServiceContract
     * @throws NotFoundException|BindingResolutionException|InvalidArgumentException|NotFoundException|UnexpectedValueException
     */
    public function workWith(string $id): SessionServiceContract;

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException|UnexpectedValueException|InvalidArgumentException
     */
    public function workWithActive(): SessionServiceContract;

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException|NonUniqueResultException|NotFoundException|UnexpectedValueException|InvalidArgumentException
     */
    public function workWithDead(): SessionServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return SessionReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): SessionReadonlyContract;

    /**
     * @return SessionDTO
     * @throws BindingResolutionException|NotFoundException|UnexpectedValueException|InvalidArgumentException|PropertyNotInit|RuntimeException
     */
    public function dto(): SessionDTO;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws BindingResolutionException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws UnexpectedValueException
     * @throws InvalidArgumentException
     */
    public function listRO(): Collection;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @param UserReadonlyContract $user
     * @param array                $data
     *
     * @return SessionServiceContract
     * @throws NonUniqueResultException|NotFoundException|BindingResolutionException
     */
    public function create(UserReadonlyContract $user, array $data): SessionServiceContract;

    /**
     * @param array $data
     *
     * @return SessionServiceContract
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws PropertyNotInit
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function change(array $data): SessionServiceContract;

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function remove(): SessionServiceContract;

    /**
     * @return SessionServiceContract
     * @throws NonUniqueResultException|NotFoundException|BindingResolutionException|PropertyNotInit|RuntimeException|InvalidArgumentException
     */
    public function start(): SessionServiceContract;

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function end(): SessionServiceContract;

    /**
     * @param bool $storeEntire
     *
     * @return SessionServiceContract
     * @throws BindingResolutionException|PropertyNotInit|RuntimeException
     */
    public function wrap(bool $storeEntire = true): SessionServiceContract;

    /**
     * @return SessionServiceContract
     * @throws BindingResolutionException|PropertyNotInit|Exception
     */
    public function touch(): SessionServiceContract;

    /**
     * @return PoiServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function poiService(): PoiServiceContract;

    /**
     * @return StreamServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function streamService(): StreamServiceContract;

    /**
     * @return NoteServiceContract
     */
    public function noteService(): NoteServiceContract;

    /**
     * @return SOAPServiceContract
     */
    public function SOAPService(): SOAPServiceContract;

    /**
     * @return ProgressServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function progressService(): ProgressServiceContract;

    /**
     * @return GoalServiceContract
     * @throws BindingResolutionException|PropertyNotInit
     */
    public function goalService(): GoalServiceContract;
}
