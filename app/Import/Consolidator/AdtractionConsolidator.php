<?php

declare(strict_types=1);

namespace App\Import\Consolidator;

use App\Exceptions\FileParseException;
use App\Import\Dto\FeedDownloadTo;
use App\Import\Dto\FileParseTo;
use App\Import\Parser\CsvFileParser;
use ParseCsv\Csv;

class AdtractionConsolidator implements FeedConsolidatorInterface
{
    private CsvFileParser $fileParser;

    /**
     * AdtractionConsolidator constructor.
     * @param  CsvFileParser  $fileParser
     */
    public function __construct(CsvFileParser $fileParser)
    {
        $this->fileParser = $fileParser;
    }

    /**
     * @param  FeedDownloadTo  $downloadTo
     * @throws FileParseException
     */
    public function consolidate(FeedDownloadTo $downloadTo): void
    {
        $parseFileTo = (new FileParseTo())
            ->setFeedType('csv')
            ->setFileLocation($downloadTo->getDownloadLocationTo());

        $rows = $this->fileParser->parseFile($parseFileTo);

        $csv = new Csv();
        $titles = [];

        foreach ($rows as $row) {
            // Check if 'Extras' column exists
            if (!isset($row['Extras']) || empty(trim($row['Extras']))) {
                error_log("Skipping row, 'Extras' column is missing or empty: " . json_encode($row));
                continue;  // Skip this row or handle it differently
            }

            $extraInfo = trim($row['Extras'], '{}');
            $extraInfoData = explode('}{', $extraInfo);
            unset($row['Extras']);

            foreach ($extraInfoData as $fieldValue) {
                $extraInfoField = explode('#', $fieldValue);

                // Ensure field contains a valid key-value pair
                if (count($extraInfoField) === 2) {
                    $row[$extraInfoField[0]] = $extraInfoField[1];
                } else {
                    error_log("Invalid extra field format: " . $fieldValue);
                }
            }

            if (empty($titles)) {
                $csv->titles = array_keys($row);
            }

            if (count($row) === count($csv->titles)) {
                $csv->data[] = $row;
            }
        }

        $csv->save($downloadTo->getDownloadLocationTo());
    }
}
