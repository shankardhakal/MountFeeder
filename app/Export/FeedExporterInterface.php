<?php

namespace App\Export;

interface FeedExporterInterface
{
    /**
     * @param  FeedExportTo  $exportTo
     * @return mixed
     */
    public function export(FeedExportTo $exportTo): int;
}
