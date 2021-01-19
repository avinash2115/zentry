<?php

namespace App\Components\Sessions\Jobs;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Files\Services\Traits\LocalFileServiceTrait;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApiResponseBuilder;
use App\Components\Sessions\Jobs\Storages\Cloud;
use App\Components\Sessions\Jobs\Storages\S3;
use App\Components\Sessions\Jobs\Stream\Audio\Convert;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\Session\Stream\StreamReadonlyContract;
use App\Components\Users\User\Storage\StorageReadonlyContract;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Storage\File\DeleteException;
use App\Convention\Exceptions\Storage\File\ReadException;
use App\Convention\Exceptions\Storage\File\UploadException;
use App\Convention\Generators\Identity\IdentityGenerator;
use App\Convention\Jobs\Base\Job;
use App\Convention\ValueObjects\Identity\Identity;
use Exception;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Exception\InvalidArgumentException as FFMpegInvalidArgumentException;
use FFMpeg\Exception\RuntimeException as FFMpegRuntimeException;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use Flusher;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use InvalidArgumentException;
use Log;
use RuntimeException;

/**
 * Class PostProcess
 *
 * @package App\Components\Sessions\Jobs
 */
class PostProcess extends Job
{
    use LinkParametersTrait;
    use LocalFileServiceTrait;
    use SessionServiceTrait;
    use FileServiceTrait;

    private const FORMAT_ORIGINAL = '.webm';

    public const FORMAT_DESTINATION = '.mp4';

    /**
     * @var Identity
     */
    private Identity $sessionIdentity;

    /**
     * @var bool
     */
    private bool $storeEntire;

    /**
     * @param Identity $sessionIdentity
     * @param bool     $storeEntire
     */
    public function __construct(Identity $sessionIdentity, bool $storeEntire = true)
    {
        $this->sessionIdentity = $sessionIdentity;
        $this->storeEntire = $storeEntire;
    }

    /**
     * @inheritDoc
     */
    protected function _handle(): void
    {
        $this->sessionService__()->workWith($this->sessionIdentity);

        if (!$this->sessionService__()->readonly()->isWrapped()) {
            throw new RuntimeException("Session is not wrapped.");
        }

        app()->singleton(
            JsonApiResponseBuilder::class,
            static function () {
                return new JsonApiResponseBuilder('*', []);
            }
        );

        $schemaBase = str_replace('://', '', env('DOMAIN_SCHEMA'));

        if (is_string($schemaBase)) {
            app('url')->forceScheme($schemaBase);
        }

        app('url')->forceRootUrl(env('DOMAIN_SCHEMA') . env('DOMAIN_BASE_API'));

        $this->linkParameters__()->put(
            collect(
                [
                    'sessionId' => $this->sessionIdentity->toString(),
                ]
            )
        );

        $stream = $this->sessionService__()->streamService()->workWithType(
            StreamReadonlyContract::COMBINED_TYPE
        );

        if (!$stream->readonly()->isConverted()) {
            $this->convert();
        }

        $this->pois();

        if (!$this->storeEntire && $this->sessionService__()->readonly()->pois()->count()) {
            $this->sessionService__()->streamService()->workWithType(
                StreamReadonlyContract::COMBINED_TYPE
            )->remove();
        }

        if (!$this->sessionService__()->readonly()->user()->enabledStorage()->isDriver(
            StorageReadonlyContract::DRIVER_DEFAULT
        )) {
            $chainedJobs = [
                new Cloud($this->sessionIdentity),
                new S3($this->sessionIdentity),
            ];
        } else {
            $chainedJobs = [
                new S3($this->sessionIdentity),
            ];
        }

        dispatch(new Convert($this->sessionIdentity))->chain($chainedJobs);
    }

    /**
     * @throws BindingResolutionException
     * @throws FFMpegInvalidArgumentException
     * @throws FFMpegRuntimeException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws NotFoundException
     */
    private function convert(): void
    {
        $stream = $this->sessionService__()->streamService()->workWithType(
            StreamReadonlyContract::COMBINED_TYPE
        )->readonly();

        $path = $stream->url();

        $destinationPath = str_replace(pathinfo($path)['filename'], IdentityGenerator::next()->toString(), $path);

        try {
            $video = FFMpeg::create(
                [
                    'ffmpeg.threads' => 12,
                    'timeout' => 0,
                ],
                env('APP_DEBUG') ? app()->make('log') : null
            )->open($this->localFileService__()->path($path));

            $video->getFFMpegDriver()->command(
                [
                    '-i',
                    $this->localFileService__()->path($path),
                    '-vcodec',
                    'copy',
                    '-acodec',
                    'copy',
                    $this->localFileService__()->path($destinationPath),
                ]
            );

            $this->localFileService__()->remove($path);
            $this->localFileService__()->moveRaw($destinationPath, $path);
        } catch (Exception $ex) {
            Log::error($ex->getMessage());
        }

        $video = FFMpeg::create(
            [
                'ffmpeg.threads' => 12,
                'timeout' => 0,
            ],
            env('APP_DEBUG') ? app()->make('log') : null
        )->open($this->localFileService__()->path($path));

        $codecs = new X264('aac');

        $codecs->setAdditionalParameters(
            [
                '-r',
                '10',
                '-cpu-used',
                '5',
                '-crf',
                '20',
                '-strict',
                'experimental',
                '-movflags',
                '+faststart',
                '-pix_fmt',
                'yuv420p',
                '-profile:v',
                'baseline',
                '-max_muxing_queue_size',
                '9999'
            ]
        );

        $codecs->on(
            'progress',
            function ($video, $format, $percentage) {
                Log::info($percentage);

                if ((int)$percentage !== 100) {
                    $this->sessionService__()->streamService()->change(['progress' => (int)$percentage]);

                    Flusher::flush();
                    Flusher::commit();
                }
            }
        );

        $destinationPath = str_replace(
            [pathinfo($path)['filename'], self::FORMAT_ORIGINAL],
            [IdentityGenerator::next()->toString(), self::FORMAT_DESTINATION],
            $path
        );

        $video->save(
            $codecs,
            $this->localFileService__()->path($destinationPath, true)
        );

        $this->sessionService__()->streamService()->change(
            [
                'name' => str_replace(
                    self::FORMAT_ORIGINAL,
                    self::FORMAT_DESTINATION,
                    $stream->name()
                ),
                'url' => $destinationPath,
                'progress' => 100,
            ]
        );

        $this->localFileService__()->remove($path);

        Flusher::flush();
        Flusher::commit();
    }

    /**
     * @throws BindingResolutionException
     * @throws FFMpegInvalidArgumentException
     * @throws FFMpegRuntimeException
     * @throws InvalidArgumentException
     * @throws DeleteException
     * @throws ReadException
     * @throws UploadException
     * @throws FileNotFoundException
     * @throws RuntimeException
     * @throws NotFoundException
     */
    private function pois(): void
    {
        $stream = $this->sessionService__()->streamService()->workWithType(
            StreamReadonlyContract::COMBINED_TYPE
        )->readonly();

        $video = FFMpeg::create(
            [
                'ffmpeg.threads' => 12,
                'timeout' => 0,
            ],
            env('APP_DEBUG') ? app()->make('log') : null
        )->open($this->localFileService__()->path($stream->url()));

        if ($video instanceof Video) {
            $this->sessionService__()->readonly()->pois()->sortBy(
                function (PoiReadonlyContract $poi) {
                    return $poi->startedAt()->getTimestamp();
                }
            )->each(
                function (PoiReadonlyContract $poi) use ($video) {
                    $this->sessionService__()->poiService()->workWith($poi->identity());

                    if ($this->sessionService__()->poiService()->readonly()->thumbnail() === null) {
                        $name = $poi->identity()->toString() . '.jpg';

                        $thumbnailPath = $this->sessionService__()->poiService()->filePath(
                            $name
                        );

                        $video->frame(
                            TimeCode::fromSeconds(
                                $poi->startedAt()->getTimestamp() - $this->sessionService__()
                                    ->readonly()
                                    ->startedAt()
                                    ->getTimestamp()
                            )
                        )->save(
                            $this->localFileService__()->path($thumbnailPath, true),
                            true
                        );

                        $this->uploadAndRidLocally(
                            $this->sessionService__()->poiService()->fileNamespace(),
                            $name,
                            $thumbnailPath
                        );

                        $this->sessionService__()->poiService()->workWith($poi->identity())->change(
                            [
                                'thumbnail' => $thumbnailPath,
                            ]
                        );
                    }

                    if ($this->sessionService__()->poiService()->readonly()->stream() === null) {
                        $streamPath = $this->sessionService__()->poiService()->filePath(
                            $poi->identity()->toString() . self::FORMAT_DESTINATION
                        );

                        $codecs = new X264('aac');
                        $codecs->setAdditionalParameters(
                            [
                                '-r',
                                '10',
                                '-cpu-used',
                                '5',
                                '-crf',
                                '20',
                                '-strict',
                                'experimental',
                                '-movflags',
                                '+faststart',
                                '-pix_fmt',
                                'yuv420p',
                                '-profile:v',
                                'baseline',
                            ]
                        );

                        $video->clip(
                            TimeCode::fromSeconds(
                                $poi->startedAt()->getTimestamp() - $this->sessionService__()
                                    ->readonly()
                                    ->startedAt()
                                    ->getTimestamp()
                            ),
                            TimeCode::fromSeconds($poi->duration())
                        )->save(
                            $codecs,
                            $this->localFileService__()->path($streamPath, true)
                        );

                        $this->sessionService__()->poiService()->workWith($poi->identity())->change(
                            [
                                'stream' => $streamPath,
                            ]
                        );
                    }

                    Flusher::flush();
                    Flusher::commit();
                }
            );

            if ($this->sessionService__()->readonly()->pois()->count()) {
                $poi = $this->sessionService__()->readonly()->pois()->sortBy(
                    static function (PoiReadonlyContract $poi) {
                        return $poi->startedAt()->getTimestamp();
                    }
                )->first(
                    static function (PoiReadonlyContract $poi) {
                        return $poi->thumbnail() !== null;
                    }
                );

                if ($poi instanceof PoiReadonlyContract) {
                    $this->sessionService__()->change(['thumbnail' => $poi->thumbnail()]);
                }
            }

            if ($this->sessionService__()->readonly()->thumbnail() === null) {
                $name = $this->sessionService__()->readonly()->identity()->toString() . '.jpg';

                $thumbnailPath = $this->sessionService__()->filePath($name);

                $video->frame(
                    TimeCode::fromSeconds(1)
                )->save(
                    $this->localFileService__()->path($thumbnailPath, true),
                    true
                );

                $this->uploadAndRidLocally($this->sessionService__()->fileNamespace(), $name, $thumbnailPath);

                $this->sessionService__()->change(['thumbnail' => $thumbnailPath]);
            }

            Flusher::flush();
            Flusher::commit();
        }
    }

    /**
     * @param string $fileNamespace
     * @param string $name
     * @param string $thumbnailPath
     *
     * @throws BindingResolutionException
     * @throws DeleteException
     * @throws FileNotFoundException
     * @throws ReadException
     * @throws UploadException
     * @throws RuntimeException
     */
    private function uploadAndRidLocally(string $fileNamespace, string $name, string $thumbnailPath): void
    {
        $localFileService = get_class($this->localFileService__()->adapter());

        if (!$this->fileService__()->adapter() instanceof $localFileService) {
            $this->fileService__()->putContents(
                $fileNamespace,
                $name,
                $name,
                $this->localFileService__()->asResource($thumbnailPath)
            );

            $this->localFileService__()->remove($thumbnailPath);
        }
    }
}
