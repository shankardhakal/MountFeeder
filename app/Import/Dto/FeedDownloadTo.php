<?php

declare(strict_types=1);

namespace App\Import\Dto;

use App\Admin\Models\Feed;
use App\Import\FeedFileHelper;

class FeedDownloadTo
{
    /**
     * @var string
     */
    private string $url;

    /**
     * @var string
     */
    private string $downloadLocationTo;

    /**
     * @var string
     */
    private string $feedType;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param  string  $url
     * @return $this
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getDownloadLocationTo(): string
    {
        return $this->downloadLocationTo;
    }

    /**
     * @param  string  $downloadLocationTo
     * @return $this
     */
    public function setDownloadLocationTo(string $downloadLocationTo): self
    {
        $this->downloadLocationTo = $downloadLocationTo;

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
     * @return FeedDownloadTo
     */
    public function setFeedType(string $feedType): self
    {
        $this->feedType = $feedType;

        return $this;
    }

    /**
     * @param  Feed  $feed
     * @return static
     */
    public static function buildFrom(Feed $feed): self
    {
        return (new self())
            ->setUrl($feed->get(Feed::FIELD_FEED_URL))
            ->setDownloadLocationTo(
                FeedFileHelper::getFeedDownloadLocation($feed)
            )
            ->setFeedType($feed->get(Feed::FIELD_TYPE));
    }
}
