<?php

declare(strict_types=1);

namespace App\Observers;

use App\Admin\Models\Network;
use App\Exceptions\FeedDownloadException;
use App\Import\Download\FileDownloader;
use App\Import\Dto\FeedDownloadTo;
use App\Import\FeedFileHelper;
use App\Import\FeedService\FeedServiceInterface;

class NetworkObserver
{
    /**
     * @var FileDownloader
     */
    protected FileDownloader $downloader;

    /**
     * NetworkCreating constructor.
     * @param  FileDownloader  $downloader
     */
    public function __construct(FileDownloader $downloader)
    {
        $this->downloader = $downloader;
    }

    /**
     * @param  Network  $network
     * @throws FeedDownloadException
     */
    public function saving(Network $network): void
    {
        $fileSavePath = FeedFileHelper::getFeedFilePathByFileName($network->name);

        $downloadTo = (new FeedDownloadTo())
            ->setUrl($network->getAttribute(Network::FIELD_SAMPLE_FEED_URL))
            ->setDownloadLocationTo($fileSavePath);

        $this->downloader->download($downloadTo);

        /** @var FeedServiceInterface $service */
        $service = app(FeedServiceInterface::class, [$network]);

        $network->setAttribute(
            Network::FIELD_FIELDS,
            json_encode($service->getTitles($fileSavePath))
        );
    }
}
