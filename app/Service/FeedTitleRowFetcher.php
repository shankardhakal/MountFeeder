<?php

declare(strict_types=1);

namespace App\Service;

use App\Exceptions\FeedDownloadException;
use App\Import\Download\FileDownloader;
use App\Import\Dto\FeedDownloadTo;
use App\Import\Enum\FeedTypeEnum;
use App\Import\FeedService\CsvFeedService;
use App\Import\FeedService\FeedServiceInterface;
use App\Repository\FeedRepository;

/**
 * Class FeedTitleRowFetcher.
 */
class FeedTitleRowFetcher
{
    protected const FEED_SERVICES = [
        FeedTypeEnum::FEED_TYPE_CSV => CsvFeedService::class,
    ];

    private FileDownloader $downloader;

    private FeedRepository $feedRepository;

    /**
     * AddFeedMapping constructor.
     * @param  FileDownloader  $downloader
     * @param  FeedRepository  $feedRepository
     */
    public function __construct(
        FileDownloader $downloader,
        FeedRepository $feedRepository
    ) {
        $this->downloader = $downloader;
        $this->feedRepository = $feedRepository;
    }

    /**
     * @param  int  $feedId
     * @return array
     *
     * @throws FeedDownloadException
     */
    public function fetch(int $feedId): array
    {
        $feed = $this->feedRepository
            ->findById($feedId);

        $downloadFeedTo = FeedDownloadTo::buildFrom($feed);

        $this->downloader->download($downloadFeedTo);

        /** @var FeedServiceInterface $feedService */
        $feedService = app(FeedServiceInterface::class, [$feed]);

        return $feedService->getTitles($downloadFeedTo->getDownloadLocationTo());
    }
}
