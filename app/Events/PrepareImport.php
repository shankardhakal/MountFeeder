<?php

namespace App\Events;

use App\Admin\Models\Feed;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;

class PrepareImport
{
    use Dispatchable;

    private Feed $feed;

    /**
     * PrepareImport constructor.
     * @param  Feed  $feed
     */
    public function __construct(Feed $feed)
    {
        $this->feed = $feed;
    }

    /**
     * @return Feed
     */
    public function getFeed(): Feed
    {
        return $this->feed;
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
