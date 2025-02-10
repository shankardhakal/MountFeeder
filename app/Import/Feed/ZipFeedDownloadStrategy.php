<?php

declare(strict_types=1);

namespace App\Import\Feed;

use App\Import\Dto\FeedDownloadTo;
use App\Logger\Logger;

class ZipFeedDownloadStrategy implements FeedExtractStrategyInterface
{
    /**
     * @param  FeedDownloadTo  $feedDownloadTo
     */
    public function extract(FeedDownloadTo $feedDownloadTo): void
    {
        Logger::info('EXTRACT_ZIP_FEED', ['feed_url'=>$feedDownloadTo->getUrl()]);

        $fileSavePath = $feedDownloadTo->getDownloadLocationTo();

        $zip = new \ZipArchive();

        $zip->open($fileSavePath);
        $fileNameInZip = $zip->statIndex(0)['name'];

        $zip->close();

        file_put_contents(
            $fileSavePath,
            file_get_contents("zip://{$fileSavePath}#{$fileNameInZip}")
        );
    }
}
