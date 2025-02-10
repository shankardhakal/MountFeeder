<?php

declare(strict_types=1);

namespace App\Import\Parser;

use App\Exceptions\FileParseException;
use App\Import\Dto\FileParseTo;
use App\Logger\Logger;
use ParseCsv\Csv;

class CsvFileParser implements FileParserInterface
{
    /**
     * @param  FileParseTo  $fileParseTo
     * @return \Generator
     *
     * @throws FileParseException
     */
    public function parseFile(FileParseTo $fileParseTo): \Generator
    {
        $reader = $this->getReader(
            $fileParseTo->getFileLocation(),
            0,
            $fileParseTo->getParseLimit()
        );

        foreach ($reader->data as &$data) {
            yield $data;
            unset($data);
        }
    }

    /**
     * @param  string  $filePath
     * @return array
     *
     * @throws FileParseException
     */
    public function getTitles(string $filePath): array
    {
        return $this->getReader($filePath, 0, 2)->titles;
    }

    /**
     * @param  string  $filePath
     * @param  int  $offset
     * @param  int  $limit
     *
     * @return Csv
     *
     * @throws FileParseException
     */
    protected function getReader(string $filePath, int $offset, int $limit): Csv
    {
        $reader = new Csv();

        $reader->limit = $limit;
        $reader->offset = $offset;

        $startTime = microtime(true);

        $result = $reader->auto($filePath);

        if ($result === false) {
            throw FileParseException::getCsvFileParseFailureException();
        }

        $end = microtime(true);

        Logger::info(
            'TOTAL_PARSE_TIME',
            [
                'time_spent' => $end - $startTime,
                'items_count'      => count($reader->data),
            ]
        );

        return $reader;
    }
}
