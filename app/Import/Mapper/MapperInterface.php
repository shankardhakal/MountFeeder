<?php

declare(strict_types=1);

namespace App\Import\Mapper;

use App\Woocommerce\WoocommerceProduct;

interface MapperInterface
{
    /**
     * @param  WoocommerceProduct  $product
     * @param  array  $originalData
     * @return WoocommerceProduct
     */
    public function map(WoocommerceProduct $product, array $originalData): ?WoocommerceProduct;
}
