<?php

namespace App\Components\Users\Participant\Traits;

use App\Components\Users\Participant\ParticipantReadonlyContract;
use App\Convention\Entities\Traits\CollectibleTrait;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\ValueObjects\Identity\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Illuminate\Support\Collection;

/**
 * Trait ParticipantableTrait
 *
 * @package App\Components\Users\Participant\Traits
 */
trait ParticipantableTrait
{
    use CollectibleTrait;

    /**
     * @var DoctrineCollection
     */
    private DoctrineCollection $participants;

    /**
     * @return Collection
     */
    public function participants(): Collection
    {
        return $this->doctrineCollectionToCollection($this->participants);
    }

    /**
     * @param ParticipantReadonlyContract $participant
     */
    public function addParticipant(ParticipantReadonlyContract $participant): void
    {
        if (!$this->participants()->has($participant->identity()->toString())) {
            $this->participants->set($participant->identity()->toString(), $participant);
        }
    }

    /**
     * @param Identity $identity
     *
     * @return ParticipantReadonlyContract
     * @throws NotFoundException
     */
    public function participantByIdentity(Identity $identity): ParticipantReadonlyContract
    {
        $participant = $this->participants()->get($identity->toString());

        if (!$participant instanceof ParticipantReadonlyContract) {
            throw new NotFoundException('Participant not found');
        }

        return $participant;
    }

    /**
     * @param ParticipantReadonlyContract $participant
     *
     * @throws NotFoundException
     */
    public function removeParticipant(ParticipantReadonlyContract $participant): void
    {
        $this->checkRemovalAbility($participant);

        $existed = $this->participantByIdentity($participant->identity());
        $this->participants->remove($existed->identity()->toString());
    }

    /**
     *
     */
    private function setParticipants(): void
    {
        $this->participants = new ArrayCollection();
    }
}
