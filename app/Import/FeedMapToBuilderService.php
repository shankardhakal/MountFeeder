<?php

declare(strict_types=1);

namespace App\Import;

use App\Admin\Models\Feed;
use App\Import\Dto\FeedMapTo;
use App\Repository\FeedRepository;
use App\Woocommerce\WoocommerceApi;
use Illuminate\Support\Collection;

class FeedMapToBuilderService
{
    /**
     * @param  Feed  $feed
     * @return FeedMapTo
     */
    public static function build(Feed $feed): FeedMapTo
    {
        /** @var Collection $networkMapping */
        $networkMapping = $feed->network->fieldMapping->pluck('source_field', 'woocommerce_field');

        /** @var Collection $feedMapping */
        $feedMapping = $feed->fieldMapping->pluck('source_field', 'woocommerce_field');

        $woocommerceApi = app()->make(
            WoocommerceApi::class,
            [
                $feed->website,

            ]
        );

        $woocommerceCategories = $woocommerceApi->getCategories();

        $woocommerceAttributes = $woocommerceApi->getAttributes();

        return (new FeedMapTo())
            ->setFeedSlug($feed->slug)
            ->setFeedName($feed->store_name)
            ->setCategoryMapping($feed->getCategoryMapping())
            ->setWoocommerceCategories($woocommerceCategories)
            ->setWoocommerceAttributes($woocommerceAttributes)
            ->setFeedMapping($networkMapping->merge($feedMapping))
            ->setRules(
                $feed->rules->map(function ($value) {
                    return $value['syntax'];
                })
            )
            ->setLocale($feed->website->configuration->locale ?? WebsiteLocale::LOCALE_nl_NL);
    }
}
