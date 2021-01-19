<?php

namespace App\Assistants\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Collection;

/**
 * Class EventRegistry
 *
 * @package App\Assistants\Events
 */
class EventRegistry
{
    public const SYSTEM_GROUP = 'system';

    /**
     * @var Collection
     */
    protected $eventsStack;

    /**
     * @var Collection
     */
    protected Collection $broadcastEventsStack;

    /**
     * EventRegistry constructor.
     */
    public function __construct()
    {
        $this->eventsStack = collect();
        $this->broadcastEventsStack = collect();
    }

    /**
     * close clone method
     */
    private function __clone()
    {
    }

    /**
     * Flush events stack. Set empty collection to it
     */
    public function flushEvents(): void
    {
        $this->eventsStack = collect();
        $this->broadcastEventsStack = collect();
    }

    /**
     * @param object $event
     *
     * @return bool
     */
    public function register(object $event): bool
    {
        $this->eventsStack->push($event);

        return true;
    }

    /**
     * @param ShouldBroadcast $event
     */
    public function registerBroadcast(ShouldBroadcast $event): void
    {
        $this->broadcastEventsStack->push($event);
    }

    /**
     * @param bool $forget
     *
     * @return Collection
     */
    public function list(bool $forget = false): Collection
    {
        return $this->eventsStack;
    }

    /**
     * @param bool $forget
     *
     * @return Collection
     */
    public function broadcastList(bool $forget = false): Collection
    {
        return $this->broadcastEventsStack;
    }
}
