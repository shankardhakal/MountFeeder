<?php

declare(strict_types=1);

namespace App\Import\Feed;

use App\Import\Dto\FeedDownloadTo;

interface FeedExtractStrategyInterface
{
    /**
     * @param  FeedDownloadTo  $feedDownloadTo
     */
    public function extract(FeedDownloadTo $feedDownloadTo): void;
}
