<?php

declare(strict_types=1);


use App\Import\InspireupliftProductBuilder;
use App\Import\ProductBuilder;

return [
    'default' => ProductBuilder::class,
    'petshop123-inspireuplift' => InspireupliftProductBuilder::class,
];