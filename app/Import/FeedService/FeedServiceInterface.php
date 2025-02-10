<?php

declare(strict_types=1);

namespace App\Import\FeedService;

/**
 * Interface FeedServiceInterface.
 */
interface FeedServiceInterface
{
    /**
     * @param  string  $filePath
     * @param  array  $keys
     * @return \Generator
     */
    public function extractValues(string $filePath, array $keys): \Generator;

    /**
     * @param  string  $filePath
     * @return array
     */
    public function getTitles(string $filePath): array;
}
