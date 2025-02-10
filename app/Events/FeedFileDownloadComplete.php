<?php

declare(strict_types=1);

namespace App\Events;

use App\Import\Dto\FeedDownloadTo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FeedFileDownloadComplete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected FeedDownloadTo $feedDownloadTo;

    /**
     * FeedFileDownloadComplete constructor.
     * @param  FeedDownloadTo  $feedDownloadTo
     */
    public function __construct(FeedDownloadTo $feedDownloadTo)
    {
        $this->feedDownloadTo = $feedDownloadTo;
    }

    /**
     * @return FeedDownloadTo
     */
    public function getFeedDownloadTo(): FeedDownloadTo
    {
        return $this->feedDownloadTo;
    }

    /**
     * @param  FeedDownloadTo  $feedDownloadTo
     * @return FeedFileDownloadComplete
     */
    public function setFeedDownloadTo(FeedDownloadTo $feedDownloadTo): self
    {
        $this->feedDownloadTo = $feedDownloadTo;

        return $this;
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
