<?php

namespace App\Components\Sessions\Jobs\Storages;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Files\Services\Traits\LocalFileServiceTrait;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Storage\File\ReadException;
use App\Convention\Exceptions\Storage\File\UploadException;
use App\Convention\Jobs\Base\Job;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use Flusher;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use RuntimeException;

/**
 * Class S3
 *
 * @package App\Components\Sessions\Jobs
 */
class S3 extends Job
{
    use LinkParametersTrait;
    use FileServiceTrait;
    use SessionServiceTrait;
    use UserServiceTrait;
    use LocalFileServiceTrait;

    /**
     * @var Identity
     */
    private Identity $sessionIdentity;

    /**
     * @param Identity $sessionIdentity
     */
    public function __construct(Identity $sessionIdentity)
    {
        $this->sessionIdentity = $sessionIdentity;
    }

    /**
     * @inheritDoc
     */
    protected function _handle(): void
    {
        Flusher::open();

        $namespace = $this->sessionService__()->workWith($this->sessionIdentity)->poiService()->fileNamespace();

        $this->linkParameters__()->put(
            collect(
                [
                    'sessionId' => $this->sessionIdentity->toString(),
                ]
            )
        );

        $this->sessionService__()->poiService()->listRO()->each(
            function (PoiReadonlyContract $poi) use ($namespace) {
                $this->sessionService__()->poiService()->workWith($poi->identity())->change(
                    [
                        'stream' => $this->uploadAndRidLocal($namespace, $poi->stream()),
                    ]
                );
            }
        );

        $this->sessionService__()->streamService()->listRO()->each(
            function (StreamReadonlyContract $stream) {
                $this->sessionService__()->streamService()->workWith($stream->identity())->change(
                    [
                        'url' => $this->uploadAndRidLocal($this->sessionService__()->fileNamespace(), $stream->url()),
                    ]
                );
            }
        );

        $this->userService__()->workWith($this->sessionService__()->readonly()->user()->identity());

        if ($this->userService__()->readonly()->enabledStorage()->isDriver(
                StorageReadonlyContract::DRIVER_DEFAULT
            ) || !$this->userService__()->readonly()->enabledStorage()->available()) {
            $this->userService__()->storageService()->workWithDriver(StorageReadonlyContract::DRIVER_DEFAULT)->sync(
                $this->userService__()->readonly()->fileNamespace()
            );
        }

        Flusher::flush();
        Flusher::commit();
    }

    /**
     * @param string $namespace
     * @param string $path
     *
     * @return string $url
     * @throws BindingResolutionException
     * @throws DeleteException
     * @throws FileNotFoundException
     * @throws ReadException
     * @throws UploadException
     * @throws RuntimeException
     */
    private function uploadAndRidLocal(string $namespace, string $path): string
    {
        $localFileService = get_class($this->localFileService__()->adapter());

        if (!$this->fileService__()->adapter() instanceof $localFileService) {
            if ($this->localFileService__()->isExist($path)) {
                $name = Arr::get(pathinfo($path), 'basename', []);

                $file = $this->fileService__()->putContents(
                    $namespace,
                    $name,
                    $name,
                    $this->localFileService__()->asResource($path)
                );

                $this->localFileService__()->remove($path);

                return $file->url();
            }
        }

        return $path;
    }
}
