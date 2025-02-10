<?php

namespace App\Events;

use App\Admin\Models\Feed;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var
     */
    private Feed $feed;

    /**
     * @var int
     */
    private int $totalImportCount;

    /**
     * ImportCompleted constructor.
     * @param  \App\Admin\Models\Feed  $feed
     * @param  int  $totalImportCount
     */
    public function __construct(Feed $feed, int $totalImportCount)
    {
        $this->feed = $feed;
        $this->totalImportCount = $totalImportCount;
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
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
