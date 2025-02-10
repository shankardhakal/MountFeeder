<?php

declare(strict_types=1);

namespace App\Import\Dto;

class FileParseTo
{
    private string $fileLocation;

    private int $parseLimit = PHP_INT_MAX;

    private string $feedType;

    /**
     * @return string
     */
    public function getFileLocation(): string
    {
        return $this->fileLocation;
    }

    /**
     * @param  string  $fileLocation
     * @return FileParseTo
     */
    public function setFileLocation(string $fileLocation): self
    {
        $this->fileLocation = $fileLocation;

        return $this;
    }

    /**
     * @return int
     */
    public function getParseLimit(): int
    {
        return $this->parseLimit;
    }

    /**
     * @param  int  $parseLimit
     * @return FileParseTo
     */
    public function setParseLimit(int $parseLimit): self
    {
        $this->parseLimit = $parseLimit;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeedType(): string
    {
        return $this->feedType;
    }

    /**
     * @param  string  $feedType
     * @return FileParseTo
     */
    public function setFeedType(string $feedType): self
    {
        $this->feedType = $feedType;

        return $this;
    }
}
