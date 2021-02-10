<?php

namespace App\Components\Users\Services\User;

use App\Assistants\Events\EventRegistry;
use App\Assistants\QR\ValueObjects\Url;
use App\Assistants\QR\ValueObjects\UrlPayload;
use App\Components\Users\Services\Device\DeviceServiceContract;
use App\Components\Users\Services\User\DataProvider\DataProviderServiceContract;
use App\Components\Users\Services\User\CRM\CRMServiceContract;
use App\Components\Users\Services\User\Storage\StorageServiceContract;
use App\Components\Users\User\Backtrack\BacktrackContract;
use App\Components\Users\User\Events\Created;
use App\Components\Users\User\Mutators\DTO\Mutator;
use App\Components\Users\User\Poi\PoiContract;
use App\Components\Users\User\Profile\Mutators\DTO\Mutator as ProfileMutator;
use App\Components\Users\User\Profile\ProfileContract;
use App\Components\Users\User\Profile\ProfileDTO;
use App\Components\Users\User\Repository\UserRepositoryContract;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Components\Users\User\UserContract;
use App\Components\Users\User\UserDTO;
use App\Components\Users\User\UserReadonlyContract;
use App\Components\Users\ValueObjects\Credentials;
use App\Components\Users\ValueObjects\Device\ConnectingToken;
use App\Components\Users\ValueObjects\Email;
use App\Components\Users\ValueObjects\HashedPassword;
use App\Components\Users\ValueObjects\Profile\Payload as ProfilePayload;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOServiceTrait;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\CountableTrait;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Cache;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class UserService
 */
class UserService implements UserServiceContract
{
    use SimplifiedDTOServiceTrait;
    use FilterableTrait;
    use CountableTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var ProfileMutator|null
     */
    private ?ProfileMutator $profileMutator = null;

    /**
     * @var UserRepositoryContract | null
     */
    private ?UserRepositoryContract $repository = null;

    /**
     * @var UserContract|null
     */
    private ?UserContract $entity = null;

    /**
     * @var StorageServiceContract|null
     */
    private ?StorageServiceContract $storageService = null;

    /**
     * @var DataProviderServiceContract|null
     */
    private ?DataProviderServiceContract $dataProviderService = null;

    /**
     * @var CRMServiceContract|null
     */
    private ?CRMServiceContract $crmService = null;

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
    private function setProfileMutator(): self
    {
        if (!$this->profileMutator instanceof ProfileMutator) {
            $this->profileMutator = app()->make(ProfileMutator::class);
        }

        return $this;
    }

    /**
     * @return ProfileMutator
     * @throws BindingResolutionException
     */
    private function _profileMutator(): ProfileMutator
    {
        $this->setProfileMutator();

        return $this->profileMutator;
    }

    /**
     * @return self
     * @throws BindingResolutionException
     */
    private function setRepository(): self
    {
        if (!$this->repository instanceof UserRepositoryContract) {
            $this->repository = app()->make(UserRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @return UserRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): UserRepositoryContract
    {
        $this->setRepository();

        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): UserServiceContract
    {
        $this->applyFilters([]);

        $entity = $this->_repository()->byIdentity(new Identity($id));

        return $this->setEntity($entity);
    }

    /**
     * @inheritdoc
     */
    public function workWithByEmail(string $email): UserServiceContract
    {
        $this->applyFilters([]);

        $user = $this->_repository()->filterByEmails([$email])->getOne();

        if (!$user instanceof UserContract) {
            throw new NotFoundException("User with email: {$email} not found");
        }

        return $this->setEntity($user);
    }

    /**
     * @param UserContract $user
     *
     * @return UserServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setEntity(UserContract $user): UserServiceContract
    {
        $this->entity = $user;

        $this->setStorageService();
        $this->setCRMService();
        $this->setDataProviderService();

        return $this;
    }

    /**
     * @return UserServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setStorageService(): UserServiceContract
    {
        $this->storageService = app()->make(
            StorageServiceContract::class,
            [
                'user' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function storageService(): StorageServiceContract
    {
        if (!$this->storageService instanceof StorageServiceContract) {
            $this->setStorageService();
        }

        return $this->storageService;
    }

    /**
     * @return UserServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setDataProviderService(): UserServiceContract
    {
        $this->dataProviderService = app()->make(
            DataProviderServiceContract::class,
            [
                'user' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dataProviderService(): DataProviderServiceContract
    {
        if (!$this->dataProviderService instanceof DataProviderServiceContract) {
            $this->setDataProviderService();
        }

        return $this->dataProviderService;
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
    public function readonly(): UserReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): UserDTO
    {
        $dto = $this->_mutator()->toDTO($this->_entity());

        $this->fullMutation();

        return $dto;
    }

    /**
     * @return UserContract
     * @throws PropertyNotInit
     */
    private function _entity(): UserContract
    {
        if (!$this->entity instanceof UserContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @inheritDoc
     */
    public function create(Credentials $credentials): UserServiceContract
    {
        $this->applyFilters(
            [
                'emails' => [
                    'collection' => [$credentials->email()->toString()],
                    'has' => true,
                ],
            ]
        );

        if ((bool)$this->count()) {
            throw new InvalidArgumentException("User with the {$credentials->email()} email is already registered");
        }

        $user = $this->make($credentials);

        $this->setEntity($user);

        $this->_repository()->persist($this->_entity());

        $this->storageService()->create(
            [
                'driver' => StorageReadonlyContract::DRIVER_DEFAULT,
            ]
        )->change(
            [
                'enabled' => true,
            ]
        );

        app()->make(EventRegistry::class)->register(new Created($this->readonly(), $credentials));

        return $this;
    }

    /**
     * @param Credentials $credentials
     *
     * @return UserContract
     * @throws BindingResolutionException
     * @throws RuntimeException
     */
    private function make(Credentials $credentials): UserContract
    {
        $user = app()->make(
            UserContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'credentials' => $credentials,
            ]
        );

        return $user->attachPoi($this->makePoi($user))->attachBackTrack($this->makeBacktrack($user));
    }

    /**
     * @param UserContract $user
     *
     * @return PoiContract
     * @throws BindingResolutionException
     */
    private function makePoi(UserContract $user): PoiContract
    {
        return app()->make(
            PoiContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'forward' => PoiContract::DEFAULT_FORWARD,
                'backward' => PoiContract::DEFAULT_BACKWARD,
            ]
        );
    }

    /**
     * @param UserContract $user
     *
     * @return BacktrackContract
     * @throws BindingResolutionException
     */
    private function makeBacktrack(UserContract $user): BacktrackContract
    {
        return app()->make(
            BacktrackContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'backward' => BacktrackContract::DEFAULT_BACKWARD,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): UserServiceContract
    {
        if (Arr::has($data, 'password') && Arr::get($data, 'password')) {
            $password = new HashedPassword(Arr::get($data, 'password', ''));
            $repeatPassword = new HashedPassword(Arr::get($data, 'password_repeat', ''));

            if (!$password->equals($repeatPassword)) {
                throw new InvalidArgumentException('Password must equals repeat password.');
            }

            $this->_entity()->changePassword($password);
        }

        if (Arr::has($data, 'email')) {
            $email = Arr::get($data, 'email');
            if ($email !== $this->_entity()->email()) {
                $email = new Email($email);

                $this->applyFilters(
                    [
                        'ids' => [
                            'collection' => [$this->identity()->toString()],
                            'has' => false,
                        ],
                        'emails' => [
                            'collection' => [$email->toString()],
                            'has' => true,
                        ],
                    ]
                );

                if ((bool)$this->count()) {
                    throw new InvalidArgumentException("User with the {$email} email is already registered");
                }

                $this->_entity()->changeEmail($email);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function archive(): UserServiceContract
    {
        $this->_entity()->archive();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function restore(): UserServiceContract
    {
        $this->_entity()->restore();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        $result = $this->listRO()->map(
            function (UserContract $user) {
                return $this->_mutator()->toDTO($user);
            }
        );

        $this->fullMutation();

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->handleFilters($this->filters());

        $results = $this->_repository()->getAll();

        $this->applyFilters([]);

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function attachProfile(ProfilePayload $payload): UserServiceContract
    {
        $this->_entity()->attachProfile($this->makeProfile($payload));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function profileDTO(): ProfileDTO
    {
        return $this->_profileMutator()->toDTO($this->_entity()->profileReadonly());
    }

    /**
     * @inheritDoc
     */
    public function changeProfile(ProfilePayload $payload): UserServiceContract
    {
        $profile = $this->_entity()->profile();

        if ($payload->firstName() !== $profile->firstName()) {
            $profile->changeFirstName($payload->firstName());
        }

        if ($payload->lastName() !== $profile->lastName()) {
            $profile->changeLastName($payload->lastName());
        }

        if ($payload->phoneCode() !== $profile->phoneCode()) {
            $profile->changePhoneCode($payload->phoneCode());
        }

        if ($payload->phoneNumber() !== $profile->phoneNumber()) {
            $profile->changePhoneNumber($payload->phoneNumber());
        }

        return $this;
    }

    /**
     * @param ProfilePayload $payload
     *
     * @return ProfileContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function makeProfile(ProfilePayload $payload): ProfileContract
    {
        return app()->make(
            ProfileContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $this->_entity(),
                'payload' => $payload,
            ]
        );
    }

    /**
     * @param array $filter
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotImplementedException
     * @throws UnexpectedValueException
     */
    public function handleFilters(array $filter = []): void
    {
        if (Arr::has($filter, 'ids')) {
            $needleCollection = Arr::get($filter, 'ids.collection', []);
            $isContains = filter_var(Arr::get($filter, 'ids.has', true), FILTER_VALIDATE_BOOLEAN);
            $this->_repository()->filterByIds($needleCollection, $isContains);
        }

        if (Arr::has($filter, 'emails')) {
            $needleCollection = Arr::get($filter, 'emails.collection', []);
            $isContains = filter_var(Arr::get($filter, 'emails.has', true), FILTER_VALIDATE_BOOLEAN);
            $this->_repository()->filterByEmails($needleCollection, $isContains);
        }

        if (Arr::has($filter, 'storages')) {
            $needleCollection = Arr::get($filter, 'storages.driver', []);
            $isContains = filter_var(Arr::get($filter, 'storages.has', true), FILTER_VALIDATE_BOOLEAN);
            $isEnabled = filter_var(Arr::get($filter, 'storages.enabled', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByStorageDrivers($needleCollection, $isContains, $isEnabled);
        }

        if (Arr::has($filter, 'data_providers')) {
            if (Arr::has($filter, 'data_providers.drivers')) {
                $needleCollection = Arr::get($filter, 'data_providers.drivers.collection', []);
                $isContains = filter_var(Arr::get($filter, 'data_providers.drivers.has', true), FILTER_VALIDATE_BOOLEAN);

                $this->_repository()->filterByDataProviders($needleCollection, $isContains);
            }

            if (Arr::has($filter, 'data_providers.statuses')) {
                $needleCollection = Arr::get($filter, 'data_providers.statuses.collection', []);
                $isContains = filter_var(Arr::get($filter, 'data_providers.statuses.has', true), FILTER_VALIDATE_BOOLEAN);

                $this->_repository()->filterByDataProvidersStatuses($needleCollection, $isContains);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function asQRPayload(): UrlPayload
    {
        $connectingToken = new ConnectingToken($this->_entity()->identity(), new DateTime());

        Cache::set($connectingToken->identity()->toString(), $connectingToken, $connectingToken->retentionTtl());

        return new UrlPayload(
            new Url(
                route(
                    DeviceServiceContract::ROUTE_ADD_DEVICE_BY_TOKEN,
                    ['token' => $connectingToken->identity()->toString()]
                )
            )
        );
    }

    /**
     * @return UserServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    private function setCRMService(): UserServiceContract
    {
        $this->crmService = app()->make(
            CRMServiceContract::class,
            [
                'user' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function crmService(): CRMServiceContract
    {
        if (!$this->crmService instanceof CRMServiceContract) {
            $this->setCRMService();
        }

        return $this->crmService;
    }
}
