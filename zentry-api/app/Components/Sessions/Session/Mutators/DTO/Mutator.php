<?php

namespace App\Components\Sessions\Session\Mutators\DTO;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Components\CRM\Source\Mutators\DTO\Traits\SourceMutatorTrait;
use App\Components\Services\Service\Mutators\DTO\Traits\MutatorTrait as ServiceMutatorTrait;
use App\Components\Services\Service\ServiceDTO;
use App\Components\Services\Service\ServiceReadonlyContract;
use App\Components\Sessions\Session\Goal\GoalReadonlyContract;
use App\Components\Sessions\Session\Goal\Mutators\DTO\Traits\MutatorTrait as GoalMutatorTrait;
use App\Components\Sessions\Session\Note\Mutators\DTO\Mutator as NoteMutator;
use App\Components\Sessions\Session\Note\NoteReadonlyContract;
use App\Components\Sessions\Session\Poi\Mutators\DTO\Traits\MutatorTrait as PoiMutatorTrait;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Progress\Mutators\DTO\Mutator as ProgressMutator;
use App\Components\Sessions\Session\Progress\ProgressReadonlyContract;
use App\Components\Sessions\Session\SessionDTO;
use App\Components\Sessions\Session\SessionReadonlyContract;
use App\Components\Sessions\Session\SOAP\Mutators\DTO\Mutator as SOAPMutator;
use App\Components\Sessions\Session\SOAP\SOAPReadonlyContract;
use App\Components\Sessions\Session\Stream\Mutators\DTO\Mutator as StreamMutator;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Users\Participant\Mutators\DTO\Traits\MutatorTrait as ParticipantMutatorTrait;
use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Components\Users\Team\School\Mutators\DTO\Traits\MutatorTrait as SchoolMutatorTrait;
use App\Components\Users\Team\School\SchoolDTO;
use App\Components\Users\Team\School\SchoolReadonlyContract;
use App\Components\Users\User\Mutators\DTO\Mutator as UserMutator;
use App\Convention\DTO\Mutators\SimplifiedDTOContract;
use App\Convention\DTO\Mutators\Traits\SimplifiedDTOTrait;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Mutator
 */
final class Mutator implements SimplifiedDTOContract
{
    use SimplifiedDTOTrait;
    use ParticipantMutatorTrait;
    use GoalMutatorTrait;
    use PoiMutatorTrait;
    use FileServiceTrait;
    use SourceMutatorTrait;
    use SchoolMutatorTrait;
    use ServiceMutatorTrait;

    public const TYPE = 'sessions';

    /**
     * @var StreamMutator
     */
    private StreamMutator $streamMutator;

    /**
     * @var ProgressMutator
     */
    private ProgressMutator $progressMutator;

    /**
     * @var NoteMutator
     */
    private NoteMutator $noteMutator;

    /**
     * @var SOAPMutator
     */
    private SOAPMutator $soapMutator;

    /**
     * Mutator constructor.
     *
     * @throws BindingResolutionException
     */
    public function __construct()
    {
        $this->streamMutator = app()->make(StreamMutator::class);
        $this->progressMutator = app()->make(ProgressMutator::class);
        $this->noteMutator = app()->make(NoteMutator::class);
        $this->soapMutator = app()->make(SOAPMutator::class);
        $this->soapMutator->simplifiedMutation();
    }

    /**
     * @param SessionReadonlyContract $entity
     *
     * @return SessionDTO
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function toDTO(SessionReadonlyContract $entity): SessionDTO
    {
        $dto = new SessionDTO();
        $dto->id = $entity->identity()->toString();
        $dto->name = $entity->name();
        $dto->type = $entity->type();
        $dto->status = $entity->status();
        $dto->description = $entity->description();
        $dto->geo = $entity->geo();
        $dto->tags = $entity->tags();

        if ($entity->isWrapped() && $entity->thumbnail() !== null) {
            $dto->thumbnailURL = $this->fileService__()->temporaryUrl($entity->thumbnail(), $dto->id, 600)->url();
        }

        $dto->startedAt = dateTimeFormatted($entity->startedAt());
        $dto->endedAt = dateTimeFormatted($entity->endedAt());

        $dto->scheduledOn = dateTimeFormatted($entity->scheduledOn());
        $dto->scheduledTo = dateTimeFormatted($entity->scheduledTo());

        $dto->sign = $entity->sign();
        $dto->excludedGoals = $entity->excludedGoals();

        $dto->createdAt = dateTimeFormatted($entity->createdAt());
        $dto->updatedAt = dateTimeFormatted($entity->updatedAt());

        $this->fill($dto, $entity);

        $dto->user = app()->make(UserMutator::class)->toDTO($entity->user());

        $dto->pois = collect();
        $dto->streams = collect();
        $dto->participants = collect();
        $dto->progress = collect();
        $dto->goals = collect();

        $dto->notes = $entity->notes()->map(
            function (NoteReadonlyContract $note) {
                return $this->noteMutator->toDTO($note);
            }
        );

        $dto->soaps = $entity->soaps()->map(
            function (SOAPReadonlyContract $entity) {
                return $this->soapMutator->toDTO($entity);
            }
        );

        if (!$this->isSimplifiedMutation()) {
            $dto->pois = $entity->pois()->map(
                function (PoiReadonlyContract $poi) {
                    return $this->poiMutator__()->toDTO($poi);
                }
            );

            $dto->streams = $entity->streams()->map(
                function (StreamReadonlyContract $stream) {
                    return $this->streamMutator->toDTO($stream);
                }
            );

            $this->participantMutator__()->simplifiedMutation();

            $dto->participants = $entity->participants()->map(
                function (ParticipantReadonlyContract $participant) {
                    return $this->participantMutator__()->toDTO($participant);
                }
            );

            $dto->progress = $entity->progress()->map(
                function (ProgressReadonlyContract $progress) {
                    return $this->progressMutator->toDTO($progress);
                }
            );

            $dto->goals = $entity->goals()->map(
                function (GoalReadonlyContract $goal) {
                    return $this->sessionGoalMutator__()->toDTO($goal);
                }
            );
        }

        $this->serviceMutator__()->simplifiedMutation();

        $dto->service = $entity->service() instanceof ServiceReadonlyContract ? $this->serviceMutator__()->toDTO($entity->service()) : null;

        if ($dto->service instanceof ServiceDTO) {
            $dto->service->disableLinks();
        }

        $this->schoolMutator__()->simplifiedMutation();

        $dto->school = $entity->school() instanceof SchoolReadonlyContract ? $this->schoolMutator__()->toDTO($entity->school()) : null;

        if ($dto->school instanceof SchoolDTO) {
            $dto->school->disableLinks();
        }

        return $dto;
    }
}
