<?php

declare(strict_types=1);

namespace App\Export;

use App\Import\Resolver\Attributes;
use App\Logger\Logger;
use App\Woocommerce\WoocommerceApi;

class WoocommerceDataExport implements FeedExporterInterface
{
    private WoocommerceApi $woocommerceApi;

    protected array $existingProductSkus = [];

    /**
     * WoocommerceDataExporter constructor.
     * @param  WoocommerceApi  $woocommerceApi
     */
    public function __construct(WoocommerceApi $woocommerceApi)
    {
        $this->woocommerceApi = $woocommerceApi;
    }

    /**
     * @param  FeedExportTo  $exportTo
     * @return mixed|void
     */
    public function export(FeedExportTo $exportTo): int
    {
        if(empty($exportTo->getData())){
            return 0;
        }

        $existingSkus = $this->getExistingProductsSkus($exportTo);

        return $this->woocommerceApi->handleProducts($exportTo->getData(), $existingSkus);
    }

    /**
     * @param  FeedExportTo  $exportTo
     * @return array
     */
    private function getExistingProductsSkus(FeedExportTo $exportTo): array
    {
        if (! empty($this->existingProductSkus)) {
            return $this->existingProductSkus;
        }

        Logger::info('GET_PRODUCT_DATA_FROM_SHOP');

        $locale = $exportTo->getFeed()->website->configuration->locale;

        $attributeSlug = Attributes::ATTRIBUTES_BY_LOCALE[$locale][Attributes::ATTRIBUTE_SELLER];

        $attributeTerm = $this->woocommerceApi->getAttributeTerm($exportTo->getFeed()->slug, $attributeSlug);

        if ($attributeTerm->isEmpty()) {
            Logger::info(
                'SHOP_ATTRIBUTE_NOT_FOUND_AT_WEBSITE',
                [
                    'attribute_term' => $attributeTerm->toArray(),
                    'attribute_slug' => $attributeSlug,
                ]
            );

            throw new \RuntimeException('SHOP_NOT_FOUND');
        }

        $filter = [
            'attribute_term' => $attributeTerm->get('id'),
            'type' => 'external',
        ];

        $products = $this->woocommerceApi->getData(WoocommerceApi::ENDPOINT_PRODUCTS, $filter);

        Logger::info('PRODUCT_DATA_FETCH_COMPLETE', ['total_product_at_website' => $products->count()]);

        $this->existingProductSkus = $products->pluck('id', 'sku')->toArray();

        $products = null;

        return $this->existingProductSkus;
    }
}
