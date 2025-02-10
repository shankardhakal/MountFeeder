<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Logger\Logger;

class FileParseException extends \Exception
{
    private const CSV_FILE_PARSE_FAILED = 'csv_file_parse_failed';

    public static function getCsvFileParseFailureException(): self
    {
        Logger::error(self::CSV_FILE_PARSE_FAILED);

        return new self(self::CSV_FILE_PARSE_FAILED);
    }
}
