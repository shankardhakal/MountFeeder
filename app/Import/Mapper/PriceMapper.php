<?php

declare(strict_types=1);

namespace App\Import\Mapper;

use App\Woocommerce\WoocommerceProduct;

class PriceMapper implements MapperInterface
{
    /**
     * @param  WoocommerceProduct  $product
     * @param  array  $originalData
     *
     * @return WoocommerceProduct|null
     */
    public function map(WoocommerceProduct $product, array $originalData): ?WoocommerceProduct
    {
        if (floatval($product->oldPrice) > $product->price) {
            $product->salePrice = $product->price;
            $product->price = $product->oldPrice;
        }

        return $product;
    }
}
