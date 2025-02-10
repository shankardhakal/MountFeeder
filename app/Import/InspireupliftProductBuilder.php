<?php

declare(strict_types=1);

namespace App\Import;

use App\Woocommerce\WoocommerceProduct;
use Illuminate\Support\Collection;

class InspireupliftProductBuilder extends ProductBuilder
{

    /**
     * @param  array  $rawData
     * @return WoocommerceProduct
     */
    public function build(array $rawData): WoocommerceProduct
    {
        $product = parent::build($rawData);

        if(
            string($product->getName())->contains('dog')
            && !string($product->getDescription())->contains('t-shirt')
        ){
            $product->setCategories('dog-supplies');

            $sku = string((string)$product->getSku())
                ->firstSegment('-')
                ->trim()
                ->__toString();

            $product->setSku($sku);
        }

        return $product;

    }
}
