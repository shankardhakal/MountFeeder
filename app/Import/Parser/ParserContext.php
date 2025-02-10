<?php

declare(strict_types=1);

namespace App\Import\Parser;

use App\Import\Dto\FileParseTo;
use App\Import\Enum\FeedTypeEnum;

class ParserContext
{
    protected const FEED_TYPE_TO_PARSER_MAPPING = [
        FeedTypeEnum::FEED_TYPE_CSV => CsvFileParser::class,
    ];

    /**
     * @param  FileParseTo[]  $fileParseTos
     * @return \Generator
     */
    public function parse(array $fileParseTos): \Generator
    {
        foreach ($fileParseTos as $fileParseTo) {
            $parsedRows = $this->resolveFileParser($fileParseTo->getFeedType())
                ->parseFile($fileParseTo);

            $chunk = [];

            foreach ($parsedRows as $parsedRow) {
                $chunk[] = $parsedRow;

                if (count($chunk) > 250) {
                    yield $chunk;

                    $chunk = [];
                }
            }

            yield $chunk;
        }
    }

    /**
     * @param  string  $feedType
     * @return FileParserInterface
     */
    private function resolveFileParser(string $feedType): FileParserInterface
    {
        $parserClass = self::FEED_TYPE_TO_PARSER_MAPPING[$feedType] ?? null;

        if (empty($parserClass)) {
            throw new \RuntimeException('UNSUPPORTED_FEED_TYPE_FOUND');
        }

        return app($parserClass);
    }
}
