<?php

declare(strict_types=1);

namespace App\Import;

use App\Woocommerce\WoocommerceProduct;
use Illuminate\Support\Collection;

class ProductBuilder
{
    protected const WOOCOMMERCE_FIELDS = [
        WoocommerceProduct::NAME,
        WoocommerceProduct::COLOR,
        WoocommerceProduct::BRAND,
        WoocommerceProduct::STATUS,
        WoocommerceProduct::DESCRIPTION,
        WoocommerceProduct::SHORT_DESCRIPTION,
        WoocommerceProduct::SKU,
        WoocommerceProduct::ALTERNATE_SKU,
        WoocommerceProduct::PRICE,
        WoocommerceProduct::OLD_PRICE,
        WoocommerceProduct::CATEGORIES,
        WoocommerceProduct::EXTERNAL_URL,
        WoocommerceProduct::IMAGE_URL,
        WoocommerceProduct::MATERIAL,
        WoocommerceProduct::DELIVERY_COST,
        WoocommerceProduct::DELIVERY_TIME,
        WoocommerceProduct::LENGTH,
        WoocommerceProduct::WIDTH,
        WoocommerceProduct::HEIGHT,
        WoocommerceProduct::WEIGHT,
        WoocommerceProduct::WEIGHT_UNIT,
        WoocommerceProduct::DIMENSION_UNIT,
    ];

    private Collection $feedMapping;

    /**
     * OneToOneMapper constructor.
     * @param  Collection  $feedMapping
     */
    public function __construct(Collection $feedMapping)
    {
        $this->feedMapping = $feedMapping;
    }

    /**
     * @param  array  $rawData
     * @return WoocommerceProduct
     */
    public function build(array $rawData): WoocommerceProduct
    {
        $productData = [];

        foreach (self::WOOCOMMERCE_FIELDS as $field) {
            $extractedFieldData = trim($rawData[$this->feedMapping->get($field)] ?? '');

            if ($field === WoocommerceProduct::SKU && empty($extractedFieldData)) {
                $extractedFieldData = trim($rawData[$this->feedMapping->get(WoocommerceProduct::ALTERNATE_SKU)] ?? '');
            }

            $productData[$field] =$extractedFieldData;
        }

        return new WoocommerceProduct($productData);
    }
}
