<?php

declare(strict_types=1);

namespace App\Import\FeedService;

use App\Import\Dto\FileParseTo;
use App\Import\Parser\CsvFileParser;
use ParseCsv\Csv;

class CsvFeedService implements FeedServiceInterface
{
    /**
     * @var CsvFileParser
     */
    protected CsvFileParser $csvFileParser;

    /**
     * CsvFeedService constructor.
     * @param  CsvFileParser  $csvFileParser
     */
    public function __construct(CsvFileParser $csvFileParser)
    {
        $this->csvFileParser = $csvFileParser;
    }

    /**
     * @param  string  $filePath
     * @param  array  $keys
     * @return \Generator
     */
    public function extractValues(string $filePath, array $keys): \Generator
    {
        $fileData = $this->csvFileParser->parseFile(
            (new FileParseTo())
            ->setFileLocation($filePath)
        );

        foreach ($fileData as $row) {
            $extractedRow = [];
            foreach ($keys as $column) {
                if (isset($row[$column])) {
                    $extractedRow[$column] = $row[$column];
                }
            }

            if (! empty($extractedRow)) {
                yield $extractedRow;
            }
        }
    }

    /**
     * @param  string  $filePath
     * @return array
     */
    public function getTitles(string $filePath): array
    {
        $reader = new Csv($filePath, 0, 2);

        $reader->auto();

        return $reader->titles;
    }
}
