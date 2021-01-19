<?php

namespace App\Components\Sessions\Http\Controllers\Transcription;

use App\Assistants\Files\Services\Traits\FileServiceTrait;
use App\Assistants\Transformers\JsonApi\Traits\LinkParametersTrait;
use App\Assistants\Transformers\ValueObjects\JsonApi;
use App\Components\Sessions\Jobs\Stream\Audio\Convert;
use App\Components\Sessions\Services\Traits\SessionServiceTrait;
use App\Components\Sessions\Services\Transcription\Traits\TranscriptionServiceTrait;
use App\Convention\Exceptions\Logic\NotImplementedException;
use App\Convention\Exceptions\Repository\NotFoundException;
use App\Convention\Exceptions\Unexpected\PropertyNotInit;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use InvalidArgumentException;
use Log;
use UnexpectedValueException;

/**
 * Class Controller
 *
 * @package App\Components\Sessions\Http\Controllers\Transcription
 */
class Controller extends BaseController
{
    use LinkParametersTrait;
    use FileServiceTrait;
    use TranscriptionServiceTrait;
    use FileServiceTrait;
    use SessionServiceTrait;

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     * @param string  $poiId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     * @throws NotFoundException
     * @throws UnexpectedValueException
     * @throws NotImplementedException
     */
    public function create(JsonApi $jsonApi, string $sessionId, string $poiId): Response
    {
        $this->sessionService__()->workWith($sessionId)->poiService()->workWith($poiId);

        $jsonApi->asJsonApiCollection()->each(
            function (JsonApi $item, string $poiId) {
                $data = [
                    'user_id' => $this->sessionService__()->readonly()->user()->identity()->toString(),
                    'session_id' => $this->sessionService__()->readonly()->identity()->toString(),
                    'poi_id' => $this->sessionService__()->poiService()->readonly()->identity()->toString(),
                    'word' => (string)$item->attributes()->get('word', ''),
                    'started_at' => (float)$item->attributes()->get('started_at', 0),
                    'ended_at' => (float)$item->attributes()->get('ended_at', 0),
                    'speaker_tag' => (int)$item->attributes()->get('speaker_tag', 0),
                ];

                $this->transcriptionService__()->create($data);

                $url = $this->sessionService__()->poiService()->readonly()->stream();

                if ($url !== null) {
                    $path = $this->sessionService__()->poiService()->filePath(
                        $poiId . Convert::FORMAT_DESTINATION
                    );

                    if ($this->fileService__()->isExist($path)) {
                        $this->fileService__()->remove($path);
                    }
                }
            }
        );

        $this->sessionService__()->poiService()->indexableService()->stateChanged();

        return $this->acknowledgeResponse();
    }

    /**
     * @param JsonApi $jsonApi
     * @param string  $sessionId
     * @param string  $poiId
     *
     * @return Response
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function failed(JsonApi $jsonApi, string $sessionId, string $poiId): Response
    {
        Log::error(
            "[Session][Transcription FAILED] Session {$sessionId}, POI {$poiId}. Message: {$jsonApi->attributes()->get('message', '')}"
        );

        return $this->acknowledgeResponse();
    }
}
