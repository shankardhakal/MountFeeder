<?php

namespace App\Events;

use App\Admin\Models\Feed;
use App\Import\Dto\FeedImportTo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportFeedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private FeedImportTo $feedImportTo;

    /**
     * ImportFeedEvent constructor.
     * @param  FeedImportTo  $feedImportTo
     */
    public function __construct(FeedImportTo $feedImportTo)
    {
        $this->feedImportTo = $feedImportTo;
    }

    /**
     * @return Feed
     */
    public function getFeed(): Feed
    {
        return $this->feedImportTo->getFeed();
    }

    /**
     * @return int
     */
    public function getImportLimit(): int
    {
        return $this->feedImportTo->getImportLimit();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
