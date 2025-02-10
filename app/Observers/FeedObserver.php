<?php

declare(strict_types=1);

namespace App\Observers;

use App\Admin\Models\Feed;
use App\Import\Resolver\Attributes;
use App\Import\WebsiteLocale;
use App\Logger\Logger;
use App\Repository\FeedRepository;
use App\Woocommerce\WoocommerceApi;
use Automattic\WooCommerce\HttpClient\HttpClientException;

class FeedObserver
{
    private FeedRepository $feedRepository;

    /**
     * FeedObserver constructor.
     * @param  FeedRepository  $feedRepository
     */
    public function __construct(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
    }

    /**
     * Handle the Feed "created" event.
     *
     * @param  Feed  $feed
     * @return void
     */
    public function created(Feed $feed): void
    {
        /** @var WoocommerceApi $woocommerceApi */
        $woocommerceApi = app(WoocommerceApi::class, [$feed->website]);

        $websiteAttributes = $woocommerceApi->getAttributes();

        $attributeName = Attributes::ATTRIBUTE_ON_WEBSITE_CREATE[$feed->website->configuration->locale ?? WebsiteLocale::LOCALE_nl_NL]['name'];

        $attributeId = $websiteAttributes->pluck('id', 'name')->get($attributeName);

        if (! empty($attributeId)) {
            $this->createFeedAttributeTerm($woocommerceApi, $feed, $attributeId);

            return;
        }

        Logger::error(
            'ATTRIBUTE_FOR_SHOP_NOT_FOUND_IN_WEBSITE',
            [
                $feed,
                'attribute_name' => $attributeName,
                'website_locale' => $feed->website->configuration->locale,
            ]
        );
    }

    /**
     * Handle the feed "updated" event.
     * @param  Feed  $feed
     */
    public function updating(Feed $feed)
    {
        if (! $feed->isDirty(
            [
            Feed::FIELD_STORE_NAME,
            Feed::FIELD_SLUG,
                          ]
        )
        ) {
            return;
        }

        /** @var WoocommerceApi $woocommerceApi */
        $woocommerceApi = app(WoocommerceApi::class, [$feed->website]);

        $websiteAttributes = $woocommerceApi->getAttributes();

        $attributeName = Attributes::ATTRIBUTE_ON_WEBSITE_CREATE[$feed->website->configuration->locale ?? WebsiteLocale::LOCALE_nl_NL]['name'];

        $attributeId = $websiteAttributes->pluck('id', 'name')->get($attributeName);

        if (empty($attributeId)) {
            Logger::error(
                'ATTRIBUTE_FOR_SHOP_NOT_FOUND_IN_WEBSITE',
                [
                    $feed,
                    'attribute_name' => $attributeName,
                    'website_locale' => $feed->website->configuration->locale,
                ]
            );

            return;
        }

        $currentFeed = $this->feedRepository->findById($feed->getId());

        $attributeTerm = $woocommerceApi->getAttributeTerm($currentFeed->getSlug(), $attributeName);

        $attributeTermId = $attributeTerm->get('id');

        if (! empty($attributeTermId)) {
            $updateData = [
                'slug' => $feed->getSlug(),
                'name' => $feed->getName(),
            ];

            $woocommerceApi->updateAttributeTerm($attributeId, $attributeTermId, $updateData);

            return;
        }

        $this->createFeedAttributeTerm($woocommerceApi, $feed, $attributeId);
    }

    /**
     * @param  WoocommerceApi  $woocommerceApi
     * @param  Feed  $feed
     * @param  int  $attributeId
     */
    private function createFeedAttributeTerm(WoocommerceApi $woocommerceApi, Feed $feed, int $attributeId)
    {
        $requestUrl = sprintf('%s/%s/terms', WoocommerceApi::ENDPOINT_PRODUCT_ATTRIBUTES, $attributeId);

        $existingAttributeTerms = $woocommerceApi->getData($requestUrl, [], false);

        foreach ($existingAttributeTerms->toArray() as $attributeTerm) {
            if (
                $attributeTerm['name'] === $feed->store_name
                || $attributeTerm['slug'] === $feed->slug
            ) {
                Logger::info('FEED_SLUG_EXIST_IN_WEBSITE', [$feed]);

                return;
            }
        }

        try {
            $payload = [
                'name' => $feed->store_name,
                'slug' => $feed->slug,
            ];

            $result = $woocommerceApi->post($requestUrl, $payload);

            Logger::info(
                'FEED_SLUG_ADDED_TO_WEBSITE',
                [
                    $feed,
                    'response' => $result,
                ]
            );
        } catch (HttpClientException $exception) {
            Logger::error(
                'ERROR_ADDING_FEED_SLUG_TO_STORE',
                [
                    [
                        $feed,
                        'error'    => $exception->getMessage(),
                        'request'  => $exception->getRequest()->getBody(),
                        'response' => $exception->getResponse()->getBody(),
                    ],
                ]
            );
        }
    }
}
