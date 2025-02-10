<?php

namespace App\Events;

use App\Admin\Models\Feed;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeedImportEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    private $eventType;

    /**
     * @var string
     */
    private $eventMessage;

    /**
     * @var Feed|null
     */
    private $feed;

    /**
     * FeedImportEvent constructor.
     * @param  string  $eventType
     * @param  string  $eventMessage
     * @param Feed|null $feed
     */
    public function __construct(string $eventType, string $eventMessage, Feed $feed = null)
    {
        $this->eventType = $eventType;
        $this->eventMessage = $eventMessage;
        $this->feed = $feed;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('feed-logs');
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * @return string
     */
    public function getEventMessage(): string
    {
        return $this->eventMessage;
    }

    /**
     * @return Feed|null
     */
    public function getFeed(): ?Feed
    {
        return $this->feed;
    }
}
