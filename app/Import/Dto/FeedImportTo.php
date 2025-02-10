<?php

declare(strict_types=1);

namespace App\Import\Dto;

use App\Admin\Models\Feed;

class FeedImportTo
{
    private Feed $feed;

    private int $importLimit = -1;

    /**
     * @return Feed
     */
    public function getFeed(): Feed
    {
        return $this->feed;
    }

    /**
     * @param  Feed  $feed
     * @return FeedImportTo
     */
    public function setFeed(Feed $feed): self
    {
        $this->feed = $feed;

        return $this;
    }

    /**
     * @return int
     */
    public function getImportLimit(): int
    {
        return $this->importLimit;
    }

    /**
     * @param  int  $importLimit
     * @return FeedImportTo
     */
    public function setImportLimit(int $importLimit): self
    {
        $this->importLimit = $importLimit;

        return $this;
    }
}
