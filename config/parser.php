<?php

use App\Woocommerce\WoocommerceProduct;

return [
  'separator' =>[
      'greater_than' => '>',
      'pipe' =>'|',
      'comma' => ',',
  ],
    'separable'=>[
        WoocommerceProduct::CATEGORIES,
        WoocommerceProduct::COLOR,
    ],
];
