<?php

namespace App\Import\Parser;

use App\Import\Dto\FileParseTo;

interface FileParserInterface
{
    /**
     * @param  FileParseTo  $fileParseTo
     * @return \Generator
     */
    public function parseFile(FileParseTo $fileParseTo): \Generator;
}
