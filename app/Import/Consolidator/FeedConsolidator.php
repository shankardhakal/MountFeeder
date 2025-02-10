<?php

declare(strict_types=1);

namespace App\Import\Consolidator;

use App\Events\FeedFileDownloadComplete;
use App\Import\Dto\FeedDownloadTo;
use App\Import\Enum\NetworksEnum;

class FeedConsolidator
{
    private const CONSOLIDATE_STRATEGY = [
NetworksEnum::NETWORK_ADTRACTION => AdtractionConsolidator::class,
    ];

    /**
     * @param  FeedFileDownloadComplete  $downloadComplete
     */
    public function handle(FeedFileDownloadComplete $downloadComplete): void
    {
        $downloadTo = $downloadComplete->getFeedDownloadTo();

        $consolidator = $this->getConsolidator($downloadTo);

        if ($consolidator instanceof FeedConsolidatorInterface) {
            $consolidator->consolidate($downloadTo);
        }
    }

    protected function getConsolidator(FeedDownloadTo $downloadTo): ?FeedConsolidatorInterface
    {
        if (stripos($downloadTo->getUrl(), NetworksEnum::NETWORK_ADTRACTION) !== false) {
            return app(self::CONSOLIDATE_STRATEGY[NetworksEnum::NETWORK_ADTRACTION]);
        }

        return null;
    }
}
