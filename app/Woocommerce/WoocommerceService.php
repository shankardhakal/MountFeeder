<?php

declare(strict_types=1);

namespace App\Woocommerce;

use App\Admin\Models\Feed;
use App\Repository\FieldMappingRepository;
use Illuminate\Support\Collection;

class WoocommerceService
{
    private FieldMappingRepository $fieldMappingRepository;

    /**
     * WoocommerceService constructor.
     * @param  FieldMappingRepository  $fieldMappingRepository
     */
    public function __construct(FieldMappingRepository $fieldMappingRepository)
    {
        $this->fieldMappingRepository = $fieldMappingRepository;
    }

    /**
     * @param  Feed  $feed
     * @return Collection
     */
    public function fetchCategoriesForFeed(Feed $feed): Collection
    {
        $fieldMapping = $this->fieldMappingRepository
            ->findMappingsByWoocommerceFieldAndFeedId(
                $feed->get(Feed::FIELD_ID),
                WoocommerceProduct::CATEGORIES
            );

        if (empty($fieldMapping)) {
            $fieldMapping = $this->fieldMappingRepository
                ->findMappingsByWoocommerceFieldAndNetworkId(
                    $feed->network->id,
                    WoocommerceProduct::CATEGORIES
                );
        }

        $mappedTo = $fieldMapping->source_field;

        if (empty($mappedTo)) {
            return collect();
        }

        /** @var WoocommerceApi $woocommerceApi */
        $woocommerceApi = app(WoocommerceApi::class, [$feed->website]);

        return $woocommerceApi->getCategories();
    }
}
