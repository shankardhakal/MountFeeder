<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\FeedFileDownloadComplete;
use App\Import\Enum\FeedMimeTypesEnum;
use App\Import\Feed\FeedExtractStrategyInterface;
use App\Import\Feed\ZipFeedDownloadStrategy;

class ExtractDownloadedFile
{
    protected const EXTRACT_STRATEGIES = [
        FeedMimeTypesEnum::FEED_MIME_TYPE => ZipFeedDownloadStrategy::class,
    ];

    protected array $feedExtractors;

    /**
     * Handle the event.
     *
     * @param  FeedFileDownloadComplete  $downloadComplete
     * @return void
     */
    public function handle(FeedFileDownloadComplete $downloadComplete)
    {
        $fileSavePath = $downloadComplete
            ->getFeedDownloadTo()
            ->getDownloadLocationTo();

        $fileMimeType = mime_content_type($fileSavePath);

        if (isset(self::EXTRACT_STRATEGIES[$fileMimeType])) {
            /** @var FeedExtractStrategyInterface $extractor */
            $extractor = app(self::EXTRACT_STRATEGIES[$fileMimeType]);

            $extractor->extract($downloadComplete->getFeedDownloadTo());
        }
    }
}
