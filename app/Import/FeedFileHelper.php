<?php

declare(strict_types=1);

namespace App\Import;

use App\Admin\Models\Feed;

class FeedFileHelper
{
    /**
     * @param  Feed  $feed
     * @return string
     */
    public static function getFeedDownloadLocation(Feed $feed): string
    {
        return $downloadLocation = sprintf('%s/%s', config('feed.feeds_location'), $feed->get(Feed::FIELD_SLUG));
    }

    /**
     * @param  string  $feedFileName
     * @return string
     */
    public static function getFeedFilePathByFileName(string $feedFileName): string
    {
        return $downloadLocation = sprintf('%s/%s', config('feed.feeds_location'), $feedFileName);
    }

    /**
     * @param  string  $filename
     * @return bool
     */
    public static function isFeedFileDownloaded(string $filename): bool
    {
        return file_exists(self::getFeedFilePathByFileName($filename));
    }
}
