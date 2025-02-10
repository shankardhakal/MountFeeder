<?php

namespace App\Listeners;

use App\Admin\Models\Website;
use App\Events\ImportFeedEvent;
use App\Import\WebsiteApiDataCacheService;
use App\Woocommerce\WoocommerceApi;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Cache;

class FetchWoocommerceData
{
    private const WOOCOMMERCE_DATA_CACHE_TTL = 60 * 60 * 24; // 24 hours

    private WebsiteApiDataCacheService $apiDataCacheService;

    /**
     * FeedMapToBuilder constructor.
     * @param  WebsiteApiDataCacheService  $apiDataCacheService
     */
    public function __construct(WebsiteApiDataCacheService $apiDataCacheService)
    {
        $this->apiDataCacheService = $apiDataCacheService;
    }

    /**
     * Handle the event.
     *
     * @param  ImportFeedEvent  $event
     * @return void
     * @throws BindingResolutionException
     */
    public function handle(ImportFeedEvent $event)
    {
        $feed = $event->getFeed();

        /** @var Website $website */
        $website = $feed->website;

        $woocommerce = $this->getWoocommerceApi($website);
        $woocommerce->getCategories();

        if (empty($categories)) {
            $categories = $woocommerce->getCategories();
            $this->apiDataCacheService->storeToCache(WoocommerceApi::ENDPOINT_PRODUCT_CATEGORIES, $categories);
        }

        $attributesCacheKey = sprintf('%s.api.attributes', $website->getAttributeValue(Website::FIELD_NAME));

        $attributes = Cache::get($attributesCacheKey, []);

        if (empty($attributes)) {
            $attributes = $woocommerce->getAttributes();
            Cache::add($attributesCacheKey, $attributes, self::WOOCOMMERCE_DATA_CACHE_TTL);
        }
    }

    /**
     * @param  Website  $website
     * @return WoocommerceApi
     * @throws BindingResolutionException
     */
    public function getWoocommerceApi(Website $website): WoocommerceApi
    {
        return app()->make(WoocommerceApi::class, [$website]);
    }
}
