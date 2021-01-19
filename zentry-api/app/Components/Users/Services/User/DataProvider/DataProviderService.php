<?php

namespace App\Components\Users\Services\User\DataProvider;

use App\Assistants\Events\EventRegistry;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Users\Exceptions\DataProvider\Auth\TokenExpired;
use App\Components\Users\Exceptions\DataProvider\ServiceUnavailableException;
use App\Components\Users\Jobs\DataProvider\Synchronize;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Services\Participant\Audience\AudiencableServiceContract;
use App\Components\Users\Services\Participant\Traits\ParticipantServiceTrait;
use App\Components\Users\Services\User\DataProvider\Sync\GoogleCalendar;
use App\Components\Users\User\DataProvider\DataProviderContract;
use App\Components\Users\User\DataProvider\DataProviderDTO;
use App\Components\Users\User\DataProvider\DataProviderReadonlyContract;
use App\Components\Users\User\DataProvider\Events\Created;
use App\Components\Users\User\DataProvider\Mutators\DTO\Mutator;
use App\Components\Users\User\UserContract;
use App\Components\Users\ValueObjects\DataProvider\Driver;
use App\Components\Users\ValueObjects\DataProvider\Sync\Event;
use App\Components\Users\ValueObjects\DataProvider\Sync\Participant;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\ValueObjects\Config\Config;
use App\Convention\ValueObjects\Config\Option;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Flusher;
use Google_Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Log;
use LogicException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class DataProviderService
 *
 * @package App\Components\Users\Services\User\DataProvider
 */
class DataProviderService implements DataProviderServiceContract
{
    use SessionServiceTrait;
    use ParticipantServiceTrait;
    use LinkParametersTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var DataProviderContract|null
     */
    private ?DataProviderContract $entity = null;

    /**
     * @var UserContract
     */
    private UserContract $user;

    /**
     * DataProviderService constructor.
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
    public function workWith(string $id): DataProviderServiceContract
    {
        return $this->setEntity($this->_user()->dataProviderByIdentity(new Identity($id)));
    }

    /**
     * @inheritDoc
     */
    public function workWithDriver(string $driver): DataProviderServiceContract
    {
        return $this->setEntity($this->_user()->dataProviderByDriver($driver));
    }

    /**
     * @return DataProviderContract
     * @throws PropertyNotInit
     */
    private function _entity(): DataProviderContract
    {
        if (!$this->entity instanceof DataProviderContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param DataProviderContract $entity
     *
     * @return DataProviderService
     */
    private function setEntity(DataProviderContract $entity): DataProviderServiceContract
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
    public function readonly(): DataProviderReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): DataProviderDTO
    {
        $this->linkParameters__()->put(collect(['userId' => $this->_user()->identity()->toString()]));

        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        $this->linkParameters__()->put(collect(['userId' => $this->_user()->identity()->toString()]));

        return $this->listRO()->map(
            fn(DataProviderReadonlyContract $entity) => $this->_mutator()->toDTO($entity)
        );
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        return $this->_user()->dataProviders();
    }

    /**
     * @inheritDoc
     */
    public function create(array $data): DataProviderServiceContract
    {
        $entity = $this->make($data);

        $this->setEntity($entity);

        $this->_user()->addDataProvider($entity);

        if (!app()->runningUnitTests()) {
            $accessToken = $this->_calendar()->login()->accessToken();

            if (is_array($accessToken)) {
                $this->changeAccessToken($accessToken);
            } else {
                Log::error("Calendar doesn't returns access tokens");

                throw new RuntimeException('Error at auth attempt.');
            }

            app()->make(EventRegistry::class)->register(new Created($this->_user()->identity(), $this->readonly()));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): DataProviderServiceContract
    {
        if (Arr::has($data, 'status')) {
            switch (true) {
                case Arr::get($data, 'status') === DataProviderReadonlyContract::STATUS_ENABLED && !$this->_entity()
                        ->isEnabled():
                    $this->_entity()->enable();
                break;
                case Arr::get($data, 'status') === DataProviderReadonlyContract::STATUS_DISABLED && !$this->_entity()
                        ->isDisabled():
                    $this->_entity()->disable();
                break;
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): DataProviderServiceContract
    {
        $this->_user()->removeDataProvider($this->_entity());

        try {
            (new GoogleCalendar($this->_entity()->config()))->revokeToken();
        } catch (Exception $exception) {
            report($exception);
        }

        $this->entity = null;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return DataProviderContract
     * @throws PropertyNotInit
     * @throws BindingResolutionException
     */
    private function make(array $data): DataProviderContract
    {
        return app()->make(
            DataProviderContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $this->_user(),
                'driver' => Arr::get($data, 'driver', ''),
                'config' => $this->makeConfig(
                    collect(Arr::get($data, 'config', []))->map(
                        static function (string $value, string $attribute) {
                            return [
                                'type' => $attribute,
                                'value' => $value,
                            ];
                        }
                    )->values()->toArray()
                ),
                'status' => Arr::get($data, 'status', 0),
            ]
        );
    }

    /**
     * @param array $options
     *
     * @return Config
     */
    private function makeConfig(array $options): Config
    {
        return new Config($options);
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
    public function sync(): DataProviderServiceContract
    {
        if (!$this->_entity()->isEnabled()) {
            throw new RuntimeException("Data provider is not enabled");
        }

        $this->syncCalendarEvents();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function drivers(): Collection
    {
        return collect(config('users.data_providers.drivers'))->map(
            fn(array $values) => new Driver(...array_values($values))
        );
    }

    /**
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws NotFoundException
     * @throws Google_Exception
     * @throws LogicException
     * @throws UnexpectedValueException
     * @throws EncryptException
     */
    private function syncCalendarEvents(): DataProviderServiceContract
    {
        $calendar = $this->_calendar();

        $this->sessionService__()->applyFilters(
            [
                'referencable' => true,
                'users' => [
                    'collection' => [$this->_user()->identity()->toString()],
                    'has' => true,
                ],
            ]
        );

        $sessions = $this->sessionService__()->listRO()->keyBy(
            fn(SessionReadonlyContract $session) => $session->reference()
        );

        try {
            $events = $calendar->events();
        } catch (ServiceUnavailableException|TokenExpired $exception) {
            $this->_entity()->notAuthorized();

            return $this;
        }

        if ($calendar->accessToken() === null) {
            $this->_entity()->notAuthorized();
        } else {
            $this->changeAccessToken($calendar->accessToken());
        }

        $events->each(
            function (Event $event) use ($sessions) {
                $session = $sessions->get($event->reference());

                try {
                    if ($session instanceof SessionReadonlyContract) {
                        if ($session->isStatus(SessionReadonlyContract::STATUS_NEW)) {
                            $this->sessionService__()->workWith($session->identity())->change(
                                [
                                    'name' => $event->name(),
                                    'scheduled_on' => $event->scheduledOn(),
                                    'scheduled_to' => $event->scheduledTo(),
                                ]
                            );

                            $existedParticipants = $this->sessionService__()->readonly()->participants()->keyBy(
                                fn(ParticipantReadonlyContract $participant) => $participant->email()
                            );

                            $event->participants()->each(
                                function (Participant $participant) use (&$existedParticipants) {
                                    $existedParticipants->forget($participant->email());

                                    $this->addParticipant($this->sessionService__(), $participant);
                                }
                            );

                            $existedParticipants->each(
                                function (ParticipantReadonlyContract $participant) {
                                    $this->removeParticipant($this->sessionService__(), $participant);
                                }
                            );
                        }
                    } else {
                        $this->sessionService__()->create(
                            $this->_user(),
                            [
                                'name' => $event->name(),
                                'description' => $event->description(),
                                'reference' => $event->reference(),
                                'scheduled_on' => $event->scheduledOn(),
                                'scheduled_to' => $event->scheduledTo(),
                            ]
                        );

                        $event->participants()->each(
                            fn(Participant $participant) => $this->addParticipant(
                                $this->sessionService__(),
                                $participant
                            )
                        );
                    }
                } catch (Exception $exception) {
                    report($exception);
                }

                $sessions->forget($event->reference());
            }
        );

        $sessions->filter(
            fn(SessionReadonlyContract $session) => $session->status() === SessionReadonlyContract::STATUS_NEW
        )->each(
            fn(SessionReadonlyContract $session) => $this->sessionService__()->workWith($session->identity())->remove()
        );

        return $this;
    }

    /**
     * @return GoogleCalendar
     * @throws PropertyNotInit
     */
    private function _calendar(): GoogleCalendar
    {
        return new GoogleCalendar($this->_entity()->config());
    }

    /**
     * @param array $accessToken
     *
     * @return DataProviderService
     * @throws EncryptException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     */
    private function changeAccessToken(array $accessToken): DataProviderService
    {
        $configOptions = $this->_entity()->config()->options()->filter(
            fn(Option $option) => !$option->isType(DataProviderReadonlyContract::CONFIG_ACCESS_TOKEN_KEY)
        );

        $configOptions->push(
            new Option(
                DataProviderReadonlyContract::CONFIG_ACCESS_TOKEN_KEY,
                DataProviderReadonlyContract::CONFIG_ACCESS_TOKEN_KEY,
                serialize($accessToken)
            )
        );

        $this->_entity()->changeConfig(
            new Config($configOptions->map(fn(Option $option) => $option->toArray())->toArray(), false)
        );

        return $this;
    }

    /**
     * @param AudiencableServiceContract $audiencable
     * @param Participant                $syncParticipant
     *
     * @return DataProviderService
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    private function addParticipant(
        AudiencableServiceContract $audiencable,
        Participant $syncParticipant
    ): DataProviderService {
        $participant = $this->participantOrNull($syncParticipant->email());

        if (!$participant instanceof ParticipantReadonlyContract) {
            $participant = $this->participantService__()->create($this->_user(), $syncParticipant->toArray())->readonly(
            );

            Flusher::flush();
        } else {
            $isSyncParticipantFirstName = $syncParticipant->firstName() !== null && !strEmpty($syncParticipant->firstName());
            $isParticipantFirstName = $participant->firstName() !== null && !strEmpty($participant->firstName());

            if ($isSyncParticipantFirstName && !$isParticipantFirstName) {
                $this->participantService__()->workWith($participant->identity())->change(['first_name' => $syncParticipant->firstName()]);
            }

            $isSyncParticipantLastName = $syncParticipant->lastName() !== null && !strEmpty($syncParticipant->lastName());
            $isParticipantLastName = $participant->lastName() !== null && !strEmpty($participant->lastName());

            if ($isSyncParticipantLastName && !$isParticipantLastName) {
                $this->participantService__()->workWith($participant->identity())->change(['last_name' => $syncParticipant->lastName()]);
            }
        }

        $audiencable->audienceService()->add($participant);

        return $this;
    }

    /**
     * @param AudiencableServiceContract $audiencable
     * @param ParticipantReadonlyContract $participant
     *
     * @return DataProviderService
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws PropertyNotInit
     * @throws RuntimeException
     */
    private function removeParticipant(
        AudiencableServiceContract $audiencable,
        ParticipantReadonlyContract $participant
    ): DataProviderService {
        $audiencable->audienceService()->kick($participant);

        return $this;
    }

    /**
     * @param string $email
     *
     * @return ParticipantReadonlyContract|null
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function participantOrNull(string $email): ?ParticipantReadonlyContract
    {
        $this->participantService__()->applyFilters(
            [
                'users' => [
                    'collection' => [$this->_user()->identity()->toString()],
                ],
                'emails' => [
                    'collection' => [$email],
                ],
            ]
        );

        return $this->participantService__()->listRO()->first();
    }
}
