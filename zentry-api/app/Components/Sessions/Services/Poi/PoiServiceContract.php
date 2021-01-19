<?php

namespace App\Components\Sessions\Services\Poi;

use App\Assistants\Files\Services\Contracts\HasFiles;
use App\Assistants\Files\Services\Contracts\HasTemporaryUrl;
use App\Components\Sessions\Services\Poi\Indexable\IndexableServiceContract;
use App\Components\Sessions\Services\Poi\Participant\ParticipantServiceContract;
use App\Components\Sessions\Services\Transcription\Contracts\HasTranscription;
use App\Components\Sessions\Session\Poi\PoiDTO;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Share\Contracts\SharableContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\ValueObjects\Identity\Identity;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use RuntimeException;
use UnexpectedValueException;

/**
 * Interface PoiServiceContract
 *
 * @package App\Components\Sessions\Services\Poi
 */
interface PoiServiceContract extends HasFiles, HasTemporaryUrl, SharableContract, HasTranscription
{
    /**
     * @return ParticipantServiceContract
     * @throws BindingResolutionException
     * @throws PropertyNotInit
     */
    public function participantService(): ParticipantServiceContract;

    /**
     * @param string $id
     *
     * @return PoiServiceContract
     * @throws NotFoundException|BindingResolutionException|UnexpectedValueException|InvalidArgumentException
     */
    public function workWith(string $id): PoiServiceContract;

    /**
     * @return Identity
     * @throws PropertyNotInit
     */
    public function identity(): Identity;

    /**
     * @return PoiReadonlyContract
     * @throws PropertyNotInit
     */
    public function readonly(): PoiReadonlyContract;

    /**
     * @return PoiDTO
     * @throws PropertyNotInit|BindingResolutionException
     * @throws RuntimeException|InvalidArgumentException
     */
    public function dto(): PoiDTO;

    /**
     * @return Collection
     * @throws PropertyNotInit
     */
    public function list(): Collection;

    /**
     * @return Collection
     * @throws PropertyNotInit
     */
    public function listRO(): Collection;

    /**
     * @param array $data
     *
     * @return PoiServiceContract
     * @throws NotFoundException|BindingResolutionException|PropertyNotInit|RuntimeException|InvalidArgumentException
     */
    public function change(array $data): PoiServiceContract;

    /**
     * @param array $data
     *
     * @return PoiServiceContract
     * @throws NotFoundException|BindingResolutionException|PropertyNotInit|RuntimeException|InvalidArgumentException
     */
    public function create(array $data): PoiServiceContract;

    /**
     * @return PoiServiceContract
     * @throws PropertyNotInit|NotFoundException|BindingResolutionException|InvalidArgumentException|DeleteException
     */
    public function remove(): PoiServiceContract;

    /**
     * @return IndexableServiceContract
     */
    public function indexableService(): IndexableServiceContract;
}
