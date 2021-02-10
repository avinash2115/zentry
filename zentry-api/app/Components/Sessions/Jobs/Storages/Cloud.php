<?php

namespace App\Components\Sessions\Jobs\Storages;

use App\Assistants\Files\Drivers\Kloudless\Extender;
use App\Assistants\Files\Services\Traits\CloudFileServiceTrait;
use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Files\Services\Traits\LocalFileServiceTrait;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Jobs\PostProcess;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Users\Services\User\Traits\UserServiceTrait;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Convention\Exceptions\Storage\File\ReadException;
use App\Convention\Exceptions\Storage\File\UploadException;
use App\Convention\Jobs\Base\Job;
use App\Convention\ValueObjects\Identity\Identity;
use Arr;
use DateInterval;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Log;
use RuntimeException;

/**
 * Class Cloud
 *
 * @package App\Components\Sessions\Jobs
 */
class Cloud extends Job
{
    use LinkParametersTrait;
    use CloudFileServiceTrait;
    use SessionServiceTrait;
    use UserServiceTrait;
    use LocalFileServiceTrait;
    use FileServiceTrait;

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
        $user = $this->sessionService__()->workWith($this->sessionIdentity)->readonly()->user();
        $enabledStorage = $this->userService__()->workWith($user->identity())->readonly()->enabledStorage();

        if ($enabledStorage->isDriver(StorageReadonlyContract::DRIVER_DEFAULT)) {
            Log::warning('User has a default driver. Upload is unavailable');

            return;
        }

        if (!$enabledStorage->available()) {
            Log::warning('Storage is full. Uploading cancellation.');

            return;
        }

        Extender::extend($enabledStorage->driver(), $enabledStorage->config());

        $namespace = $this->sessionService__()->poiService()->fileNamespace(true);

        $this->sessionService__()->poiService()->listRO()->each(
            function (PoiReadonlyContract $poi) use ($namespace) {
                if ($poi->stream() !== null && $this->sessionService__()
                        ->readonly()
                        ->startedAt() instanceof DateTime) {
                    $startedAtSeconds = $poi->startedAt()->getTimestamp() - $this->sessionService__()
                        ->readonly()
                        ->startedAt()
                        ->getTimestamp();

                    $startedAtMargin = new DateTime();
                    $startedAtMargin->setTime(0, 0);
                    $startedAtMargin->add(new DateInterval("PT{$startedAtSeconds}S"));

                    $endedAtSeconds = $poi->endedAt()->getTimestamp() - $this->sessionService__()
                            ->readonly()
                            ->startedAt()
                            ->getTimestamp();

                    $endedAtMargin = new DateTime();
                    $endedAtMargin->setTime(0, 0);
                    $endedAtMargin->add(new DateInterval("PT{$endedAtSeconds}S"));
                    
                    $name = $startedAtMargin->format('H i s') . ' - ' . $endedAtMargin->format('H i s') . PostProcess::FORMAT_DESTINATION;

                    $this->upload($namespace, $name, $poi->stream());
                }
            }
        );

        $this->sessionService__()->streamService()->listRO()->each(
            function (StreamReadonlyContract $stream) {
                $pathinfo = pathinfo($stream->url());

                if (Arr::has($pathinfo, 'extension')) {
                    $name = StreamReadonlyContract::TYPES_LABELS[$stream->type()] . '.' . Arr::get(
                            $pathinfo,
                            'extension'
                        );

                    $this->upload($this->sessionService__()->fileNamespace(true), $name, $stream->url());
                } else {
                    Log::warning("Cannot obtain extension for file at {$stream->url()}");
                }
            }
        );
    }

    /**
     * @param string $namespace
     * @param string $name
     * @param string $path
     *
     * @throws ReadException
     * @throws UploadException
     * @throws BindingResolutionException
     * @throws FileNotFoundException
     */
    private function upload(string $namespace, string $name, string $path): void
    {
        if ($this->localFileService__()->isExist($path)) {
            $this->cloudFileService__()->putContents(
                $namespace,
                $name,
                $name,
                $this->localFileService__()->asResource($path)
            );
        } elseif ($this->fileService__()->isExist($path)) {
            $this->cloudFileService__()->putContents(
                $namespace,
                $name,
                $name,
                $this->fileService__()->asResource($path)
            );
        } else {
            Log::warning("Cannot stat file at {$path}");
        }
    }
}
