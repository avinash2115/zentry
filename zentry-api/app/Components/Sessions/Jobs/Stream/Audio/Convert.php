<?php

namespace App\Components\Sessions\Jobs\Stream\Audio;

use Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Files\Services\Traits\LocalFileServiceTrait;
use App\Assistants\Files\ValueObjects\TemporaryUrl;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Session\Poi\PoiReadonlyContract;
use App\Components\Sessions\ValueObjects\Transcription\Transcript;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Convention\Jobs\Base\Job;
use App\Convention\ValueObjects\Identity\Identity;
use App\Cross\Events\Transcriptions\Event;
use App\Cross\ValueObjects\Transcription\Payload;
use Exception;
use FFMpeg\Exception\InvalidArgumentException as FFMpegInvalidArgumentException;
use FFMpeg\FFMpeg;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Prwnr\Streamer\Facades\Streamer;
use RuntimeException;
use UnexpectedValueException;

/**
 * Class Convert
 *
 * @package App\Components\Sessions\Jobs\Stream\Audio;
 */
class Convert extends Job
{
    use LinkParametersTrait;
    use FileServiceTrait;
    use LocalFileServiceTrait;
    use SessionServiceTrait;

    public const FORMAT_DESTINATION = '.m4a';

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
        $this->sessionService__()->workWith($this->sessionIdentity);

        $this->sessionService__()->readonly()->pois()->filter(
            function (PoiReadonlyContract $poi) {
                return $poi->isConverted() && !$this->sessionService__()
                        ->poiService()
                        ->workWith($poi->identity())
                        ->transcript() instanceof Transcript;
            }
        )->each(
            function (PoiReadonlyContract $poi) {
                $transactionPayload = new Payload(
                    route(
                        'sessions.transcription.create',
                        [
                            $this->sessionService__()->readonly()->identity()->toString(),
                            $poi->identity()->toString(),
                        ]
                    ), route(
                        'sessions.transcription.failed',
                        [
                            $this->sessionService__()->readonly()->identity()->toString(),
                            $poi->identity()->toString(),
                        ]
                    ), $this->convert($poi)->url(),
                );

                Streamer::emit(new Event($transactionPayload));
            }
        );
    }

    /**
     * @param PoiReadonlyContract $poi
     *
     * @return TemporaryUrl
     * @throws BindingResolutionException
     * @throws FFMpegInvalidArgumentException
     * @throws PropertyNotInit
     * @throws ExecutionFailureException
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws UnexpectedValueException
     * @throws Exception
     */
    private function convert(PoiReadonlyContract $poi): TemporaryUrl
    {
        $path = $poi->stream();

        $destinationName = $poi->identity()->toString() . self::FORMAT_DESTINATION;
        $destinationPath = $this->sessionService__()->poiService()->filePath($destinationName);

        if (!$this->localFileService__()->isExist($destinationPath) && !$this->fileService__()->isExist(
                $destinationPath
            )) {
            if (!$this->localFileService__()->isExist($path)) {
                $pathInfo = pathinfo($poi->stream());
                $this->localFileService__()->putContents(
                    $pathInfo['dirname'],
                    $pathInfo['basename'],
                    $pathInfo['basename'],
                    $this->fileService__()->asResource($path)
                );
            }

            $audio = FFMpeg::create(
                [
                    'ffmpeg.threads' => 12,
                    'timeout' => 0,
                ],
                env('APP_DEBUG') ? app()->make('log') : null
            )->open($this->localFileService__()->path($path));

            $audio->getFFMpegDriver()->command(
                [
                    '-i',
                    $this->localFileService__()->path($path),
                    '-vn',
                    '-c:a',
                    'copy',
                    $this->localFileService__()->path($destinationPath),
                ]
            );

            $this->fileService__()->putContents(
                $this->sessionService__()->poiService()->fileNamespace(),
                $destinationName,
                $destinationName,
                $this->localFileService__()->asResource($destinationPath)
            );

            $this->localFileService__()->remove($destinationPath);
        }

        return $this->fileService__()->temporaryUrl($destinationPath, $destinationName, 86400);
    }
}
