<?php

namespace App\Components\Users\Services\Participant;

use App\Assistants\Elastic\Exceptions\IndexNotSupported;
use App\Assistants\Elastic\Traits\IndexableTrait;
use App\Assistants\Elastic\ValueObjects\Body;
use App\Assistants\Elastic\ValueObjects\Document;
use App\Assistants\Elastic\ValueObjects\Index;
use App\Assistants\Elastic\ValueObjects\Mapping;
use App\Assistants\Elastic\ValueObjects\Mappings;
use App\Assistants\Elastic\ValueObjects\Type;
use App\Assistants\Events\EventRegistry;
use App\Components\Users\Participant\Events\Changed;
use App\Components\Users\Participant\Mutators\DTO\Mutator;
use App\Components\Users\Participant\ParticipantContract;
use App\Components\Users\Participant\ParticipantDTO;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Participant\Repository\ParticipantRepositoryContract;
use App\Components\Users\Services\Participant\IEP\IEPServiceContract;
use App\Components\Users\Services\Participant\Therapy\TherapyServiceContract;
use App\Components\CRM\Services\Source\Traits\SourceServiceTrait;
use App\Components\CRM\Services\Traits\CRMEntityGuardTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\Team\TeamReadonlyContract;
use App\Components\CRM\Source\ParticipantSourceEntity;
use App\Components\CRM\Source\SourceReadonlyContract;
use App\Components\Users\Services\Participant\Goal\GoalServiceContract;
use App\Components\Users\User\UserReadonlyContract;
use App\Convention\Exceptions\Permit\PermissionDeniedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Services\Traits\FilterableTrait;
use App\Convention\Services\Traits\GuardedTrait;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use const FILTER_VALIDATE_BOOLEAN;

/**
 * Class ParticipantService
 *
 * @package App\Components\Users\Services\Participant
 */
class ParticipantService implements ParticipantServiceContract
{
    use GuardedTrait;
    use FilterableTrait;
    use IndexableTrait;
    use CRMEntityGuardTrait;
    use SourceServiceTrait;
    use UserServiceTrait;

    /**
     * @var Mutator | null
     */
    private ?Mutator $mutator = null;

    /**
     * @var ParticipantContract | null
     */
    private ?ParticipantContract $entity = null;

    /**
     * @var TherapyServiceContract|null
     */
    private ?TherapyServiceContract $therapyService = null;

    /**
     * @var GoalServiceContract|null
     */
    private ?GoalServiceContract $goalService = null;

    /**
     * @var IEPServiceContract|null
     */
    private ?IEPServiceContract $IEPService = null;

    /**
     * @var ParticipantRepositoryContract | null
     */
    private ?ParticipantRepositoryContract $repository = null;

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
    private function setRepository(): self
    {
        if (!$this->repository instanceof ParticipantRepositoryContract) {
            $this->repository = app()->make(ParticipantRepositoryContract::class);
        }

        return $this;
    }

    /**
     * @return ParticipantRepositoryContract
     * @throws BindingResolutionException
     */
    private function _repository(): ParticipantRepositoryContract
    {
        $this->setRepository();

        return $this->repository;
    }

    /**
     * @return ParticipantContract
     * @throws PropertyNotInit
     */
    private function _entity(): ParticipantContract
    {
        if (!$this->entity instanceof ParticipantContract) {
            throw new PropertyNotInit(__METHOD__, __CLASS__);
        }

        return $this->entity;
    }

    /**
     * @param ParticipantContract $entity
     *
     * @return ParticipantServiceContract
     * @throws BindingResolutionException
     * @throws PermissionDeniedException
     * @throws NotFoundException
     */
    private function setEntity(ParticipantContract $entity): ParticipantServiceContract
    {
        $this->entity = $entity;

        $this->guardEntity(
            $this,
            function (UserReadonlyContract $user) {
                return $this->_entity()->user()->identity()->equals($user->identity());
            }
        );

        $this->setGoalService();
        $this->setIEPService();
        $this->setTherapyService();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function goalService(): GoalServiceContract
    {
        if (!$this->goalService instanceof GoalServiceContract) {
            $this->setGoalService();
        }

        return $this->goalService;
    }

    /**
     * @return ParticipantServiceContract
     * @throws BindingResolutionException
     */
    private function setGoalService(): ParticipantServiceContract
    {
        $this->goalService = app()->make(
            GoalServiceContract::class,
            [
                'participant' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function IEPService(): IEPServiceContract
    {
        if (!$this->IEPService instanceof IEPServiceContract) {
            $this->setIEPService();
        }

        return $this->IEPService;
    }

    /**
     * @return ParticipantServiceContract
     * @throws BindingResolutionException
     */
    private function setIEPService(): ParticipantServiceContract
    {
        $this->IEPService = app()->make(
            IEPServiceContract::class,
            [
                'participant' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function therapyService(): TherapyServiceContract
    {
        if (!$this->therapyService instanceof TherapyServiceContract) {
            $this->setTherapyService();
        }

        return $this->therapyService;
    }

    /**
     * @return ParticipantServiceContract
     * @throws BindingResolutionException
     */
    private function setTherapyService(): ParticipantServiceContract
    {
        $this->therapyService = app()->make(
            TherapyServiceContract::class,
            [
                'participant' => $this->_entity(),
            ]
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function workWith(string $id): ParticipantServiceContract
    {
        $this->applyFilters([]);

        $this->setEntity($this->_repository()->byIdentity(new Identity($id)));

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
    public function readonly(): ParticipantReadonlyContract
    {
        return $this->_entity();
    }

    /**
     * @inheritDoc
     */
    public function dto(): ParticipantDTO
    {
        return $this->_mutator()->toDTO($this->_entity());
    }

    /**
     * @inheritDoc
     */
    public function create(
        UserReadonlyContract $user,
        array $data,
        ?TeamReadonlyContract $team = null,
        ?SchoolReadonlyContract $school = null
    ): ParticipantServiceContract {
        $entity = $this->make($user, $data, $team, $school);

        if ($entity->email() !== null && !strEmpty($entity->email())) {
            $this->guardRepository(
                $this,
                function (UserReadonlyContract $user) {
                    $this->_repository()->filterByUserIds([$user->identity()]);
                },
                function () { },
                $user
            );

            if ($team instanceof TeamReadonlyContract && $school instanceof SchoolReadonlyContract) {
                $result = $team->schools()->first(
                    function (SchoolReadonlyContract $teamSchool) use ($school) {
                        return $teamSchool->identity()->equals($school->identity());
                    }
                );

                if (!$result instanceof SchoolReadonlyContract) {
                    throw new InvalidArgumentException("School is not belongs to team");
                }
            }

            if ($this->_repository()->isExists($entity->email())) {
                throw new InvalidArgumentException("Participant with email {$entity->email()} already exist");
            }
        }

        $this->setEntity($entity);

        $this->_repository()->persist($this->_entity());

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function remove(): ParticipantServiceContract
    {
        $this->checkRemoving($this->readonly());
        $this->_repository()->destroy($this->_entity());

        $this->stateDeletion();

        return $this;
    }

    /**
     * @param UserReadonlyContract   $user
     * @param array                  $data
     * @param TeamReadonlyContract   $team
     * @param SchoolReadonlyContract $school
     *
     * @return ParticipantContract
     * @throws BindingResolutionException
     */
    private function make(
        UserReadonlyContract $user,
        array $data,
        ?TeamReadonlyContract $team,
        ?SchoolReadonlyContract $school
    ): ParticipantContract {
        return app()->make(
            ParticipantContract::class,
            [
                'identity' => IdentityGenerator::next(),
                'user' => $user,
                'team' => $team,
                'school' => $school,
                'email' => Arr::get($data, 'email'),
                'firstName' => Arr::get($data, 'first_name'),
                'lastName' => Arr::get($data, 'last_name'),
                'phoneCode' => Arr::get($data, 'phone_code'),
                'phoneNumber' => Arr::get($data, 'phone_number'),
                'avatar' => Arr::get($data, 'avatar'),
                'gender' => Arr::get($data, 'gender'),
                'dob' => !strEmpty((string)Arr::get($data, 'dob', '')) ? new DateTime(
                    (string)Arr::get($data, 'dob')
                ) : null,
                'parentEmail' => Arr::get($data, 'parent_email'),
                'parentPhoneNumber' => Arr::get($data, 'parent_phone_number'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function change(array $data): ParticipantServiceContract
    {
        if (Arr::has($data, 'first_name')) {
            $this->_entity()->changeFirstName(Arr::get($data, 'first_name'));
        }

        if (Arr::has($data, 'last_name')) {
            $this->_entity()->changeLastName(Arr::get($data, 'last_name'));
        }

        if (Arr::has($data, 'email')) {
            $email = Arr::get($data, 'email');

            if ($email !== null && !strEmpty($email) && $email !== $this->_entity()->email() && $this->_repository()
                    ->isExists($email)) {
                throw new InvalidArgumentException("Participant with email {$email} already exist");
            }

            $this->_entity()->changeEmail($email);
        }

        if (Arr::has($data, 'phone_code')) {
            $this->_entity()->changePhoneCode(Arr::get($data, 'phone_code'));
        }

        if (Arr::has($data, 'phone_number')) {
            $this->_entity()->changePhoneNumber(Arr::get($data, 'phone_number'));
        }

        if (Arr::has($data, 'avatar')) {
            $this->_entity()->changeAvatar(Arr::get($data, 'avatar'));
        }

        if (Arr::has($data, 'gender')) {
            $this->_entity()->changeGender((string)Arr::get($data, 'gender', ''));
        }

        if (Arr::has($data, 'dob')) {
            $this->_entity()->changeDob(
                !strEmpty((string)Arr::get($data, 'dob', '')) ? new DateTime((string)Arr::get($data, 'dob')) : null
            );
        }

        if (Arr::has($data, 'parent_email')) {
            $this->_entity()->changeParentEmail((string)Arr::get($data, 'parent_email', ''));
        }

        if (Arr::has($data, 'parent_phone_number')) {
            $this->_entity()->changeParentPhoneNumber((string)Arr::get($data, 'parent_phone_number', ''));
        }

        if (Arr::has($data, 'team')) {
            $team = Arr::get($data, 'team');
            if (!$team instanceof TeamReadonlyContract && !is_null($team)) {
                throw new InvalidArgumentException('Team should be instanceof TeamReadonlyContract or null');
            }

            $this->_entity()->attachTeam($team);
        }

        if (Arr::has($data, 'school')) {
            $school = Arr::get($data, 'school');

            if (!$school instanceof SchoolReadonlyContract && !is_null($school)) {
                throw new InvalidArgumentException('School should be instanceof SchoolReadonlyContract or null');
            }

            $this->_entity()->attachSchool($school);
        }

        app()->make(EventRegistry::class)->register(new Changed($this->readonly()));

        $this->stateChanged();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function listRO(): Collection
    {
        $this->handleFilters($this->filters());

        $this->guardRepository(
            $this,
            function (UserReadonlyContract $user) {
                $this->_repository()->filterByUserIds([$user->identity()]);
            },
            function () { }
        );

        $results = $this->_repository()->getAll();

        $this->applyFilters([]);

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function list(): Collection
    {
        return $this->listRO()->map(
            function (ParticipantContract $entity) {
                return $this->_mutator()->toDTO($entity);
            }
        );
    }

    /**
     * @param array $filters
     *
     * @throws BindingResolutionException
     */
    private function handleFilters(array $filters): void
    {
        if (Arr::has($filters, 'ids')) {
            $needleScopes = Arr::get($filters, 'ids.collection', []);
            $isContains = filter_var(Arr::get($filters, 'ids.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'users')) {
            $needleScopes = Arr::get($filters, 'users.collection', []);
            $isContains = filter_var(Arr::get($filters, 'users.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByUserIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'emails')) {
            $needleScopes = Arr::get($filters, 'emails.collection', []);
            $isContains = filter_var(Arr::get($filters, 'emails.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByEmails($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'teams')) {
            $needleScopes = Arr::get($filters, 'teams.collection', []);
            $isContains = filter_var(Arr::get($filters, 'teams.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByTeamIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'schools')) {
            $needleScopes = Arr::get($filters, 'schools.collection', []);
            $isContains = filter_var(Arr::get($filters, 'schools.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterBySchoolIds($needleScopes, $isContains);
        }

        if (Arr::has($filters, 'goals')) {
            $needleScopes = Arr::get($filters, 'goals.collection', []);
            $isContains = filter_var(Arr::get($filters, 'goals.has', true), FILTER_VALIDATE_BOOLEAN);

            $this->_repository()->filterByGoalsIds($needleScopes, $isContains);
        }
    }

    /**
     * @inheritDoc
     */
    public function merge(string $referenceId): void
    {
        if ($this->readonly()->team() instanceof TeamReadonlyContract) {
            throw new InvalidArgumentException('Merge can be only without Team relation');
        }
        $this->sourceService__()->applyFilters(
            [
                'type' => [
                    'className' => ParticipantSourceEntity::class,
                    'has' => true,
                ],
                'owners' => [
                    'collection' => [$referenceId],
                    'has' => true,
                ],
                'limit' => 1,
            ]
        );
        $sources = $this->sourceService__()->listRO();

        if ($sources->isEmpty()) {
            throw new InvalidArgumentException('Reference is not imported from CRM');
        }

        $source = $sources->first();

        if ($source instanceof ParticipantSourceEntity) {
            $owner = $this->readonly();
            $this->_repository()->destroy($this->workWith($referenceId)->_entity());
            $this->sourceService__()->create(
                $source->crm(),
                $owner,
                [
                    'source_id' => $source->sourceId(),
                ]
            );
        } else {
            throw new NotFoundException('Not Found Exception');
        }
    }

    /**
     * @inheritDoc
     */
    public function asIdentity(): Identity
    {
        return $this->_entity()->identity();
    }

    /**
     * @inheritDoc
     */
    public function asType(): Type
    {
        return new Type(Mutator::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function asDocument(Index $index): Document
    {
        switch ($index->index()) {
            case Index::INDEX_FILTERS:
                $data = collect(
                    [
                        'user_id' => $this->_entity()->user()->identity()->toString(),
                        'first_name' => $this->_entity()->firstName(),
                        'last_name' => $this->_entity()->lastName(),
                        'email' => $this->_entity()->email(),
                        'created_at' => $this->_entity()->createdAt(),
                    ]
                );
            break;
            case Index::INDEX_ENTITIES:
                $data = collect(
                    [
                        'first_name' => $this->_entity()->firstName(),
                        'last_name' => $this->_entity()->lastName(),
                        'email' => $this->_entity()->email(),
                    ]
                );
            break;
            case Index::INDEX_LABELS:
                $data = collect(
                    [
                        'label' => $this->_entity()->displayName(),
                    ]
                );
            break;
            default:
                throw new IndexNotSupported($index);
        }

        return new Document(
            $index, $this->asType(), $this->_entity()->identity(), new Body($data->toArray(), $this->asMappings($index))
        );
    }

    /**
     * @inheritDoc
     */
    public function asMappings(Index $index): Mappings
    {
        switch ($index->index()) {
            case Index::INDEX_FILTERS:
                return new Mappings(
                    collect(
                        [
                            new Mapping('user_id', Mapping::TYPE_STRING),
                            new Mapping('first_name', Mapping::TYPE_STRING),
                            new Mapping('last_name', Mapping::TYPE_STRING),
                            new Mapping('email', Mapping::TYPE_STRING),
                            new Mapping('created_at', Mapping::TYPE_DATE),
                        ]
                    )
                );
            case Index::INDEX_ENTITIES:
                return new Mappings(
                    collect(
                        [
                            new Mapping('first_name', Mapping::TYPE_STRING),
                            new Mapping('last_name', Mapping::TYPE_STRING),
                            new Mapping('email', Mapping::TYPE_STRING),
                        ]
                    )
                );
            case Index::INDEX_LABELS:
                return new Mappings(
                    collect(
                        [
                            new Mapping('label', Mapping::TYPE_STRING),
                        ]
                    )
                );
            default:
                throw new IndexNotSupported($index);
        }
    }
}
