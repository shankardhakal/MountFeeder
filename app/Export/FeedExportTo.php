<?php

declare(strict_types=1);

namespace App\Export;

use App\Admin\Models\Feed;
use App\Woocommerce\WoocommerceProduct;

class FeedExportTo
{
    private array $data = [];

    private Feed $feed;

    private string $locale;

    /**
     * @return WoocommerceProduct[]
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param  WoocommerceProduct[]  $data
     * @return FeedExportTo
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return Feed
     */
    public function getFeed(): Feed
    {
        return $this->feed;
    }

    /**
     * @param  Feed  $feed
     * @return FeedExportTo
     */
    public function setFeed(Feed $feed): self
    {
        $this->feed = $feed;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param  string  $locale
     * @return FeedExportTo
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
