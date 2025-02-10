<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Admin\Models\Feed;
use App\Events\PrepareImport;
use App\Exceptions\FeedDownloadException;
use App\Import\Download\FileDownloader;
use App\Import\Dto\FeedDownloadTo;
use App\Import\FeedFileHelper;

class DownloadFeed
{
    private FileDownloader $downloader;

    /**
     * DownloadFeed constructor.
     * @param  FileDownloader  $downloader
     */
    public function __construct(FileDownloader $downloader)
    {
        $this->downloader = $downloader;
    }

    /**
     * @param  PrepareImport  $event
     * @throws FeedDownloadException
     */
    public function handle(PrepareImport $event)
    {
        $feed = $event->getFeed();

        $downloadTo = (new FeedDownloadTo())
            ->setUrl($feed->get(Feed::FIELD_FEED_URL))
            ->setDownloadLocationTo(FeedFileHelper::getFeedDownloadLocation($feed));

        $this->downloader->download($downloadTo);
    }
}
