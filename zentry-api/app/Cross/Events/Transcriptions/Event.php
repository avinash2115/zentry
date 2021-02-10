<?php

namespace App\Cross\Events\Transcriptions;

use App\Cross\ValueObjects\Transcription\Payload;
use Prwnr\Streamer\Contracts\Event as StreamerEvent;

/**
 * Class Event
 *
 * @package App\Cross\Events\Transcriptions
 */
class Event implements StreamerEvent
{
    /**
     * @var Payload
     */
    private Payload $payload;

    /**
     * Event constructor.
     *
     * @param Payload $payload
     */
    public function __construct(Payload $payload)
    {
        $this->payload = $payload;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'transcription.created';
    }

    /**
     * @inheritDoc
     */
    public function type(): string
    {
        return self::TYPE_COMMAND;
    }

    /**
     * @inheritDoc
     */
    public function payload(): array
    {
        return $this->payload->toArray();
    }
}