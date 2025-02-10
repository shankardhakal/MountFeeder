<?php

namespace App\Import\Consolidator;

use App\Import\Dto\FeedDownloadTo;

interface FeedConsolidatorInterface
{
    public function consolidate(FeedDownloadTo $downloadTo):void;
}
