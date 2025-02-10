<?php

declare(strict_types=1);

namespace App\Exceptions;

class FeedDownloadException extends \Exception
{
    public const FEED_DOWNLOAD_ERROR = 'FEED_DOWNLOAD_ERROR';

    /**
     * @return static
     */
    public static function feedDownloadError(): self
    {
        return new self(self::FEED_DOWNLOAD_ERROR);
    }
}
