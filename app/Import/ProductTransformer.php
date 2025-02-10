<?php

declare(strict_types=1);

namespace App\Import;

use App\Import\Dto\FeedMapTo;
use App\Import\Mapper\AttributeMapper;
use App\Import\Mapper\CategoryMapper;
use App\Import\Mapper\MapperInterface;
use App\Import\Mapper\PriceMapper;
use App\Import\Mapper\Rule\RuleMapper;
use App\Woocommerce\WoocommerceProduct;
use Illuminate\Contracts\Container\BindingResolutionException;

class ProductTransformer
{
    private FeedMapTo $feedMapTo;

    /**
     * @var MapperInterface[]
     */
    private array $mappers = [];

    /**
     * MapperContext constructor.
     * @param  FeedMapTo  $feedMapTo
     * @throws BindingResolutionException
     */
    public function __construct(FeedMapTo $feedMapTo)
    {
        $this->feedMapTo = $feedMapTo;
        $this->mappers = $this->getMappers();
    }

    /**
     * @param  array  $feedDataBatch
     * @return WoocommerceProduct []
     * @throws BindingResolutionException
     */
    public function transform(array $feedDataBatch): array
    {
        $mapped = [];

        $productBuilder = $this->getProductBuilder();

        foreach ($feedDataBatch as &$data) {
            $woocommerceProduct = $productBuilder->build($data);
            $woocommerceProduct->shopSlug = $this->feedMapTo->getFeedName();
            $woocommerceProduct->locale = $this->feedMapTo->getLocale();
            $woocommerceProduct->feedName = $this->feedMapTo->getFeedName();

            if ($woocommerceProduct->isValid() === false) {
                continue;
            }

            foreach ($this->mappers as $mapper) {
                if (!$woocommerceProduct instanceof WoocommerceProduct) {
                    break;
                }

                $woocommerceProduct = $mapper->map($woocommerceProduct, $data);
            }

            if ($woocommerceProduct instanceof WoocommerceProduct) {
                $mapped[] = $woocommerceProduct;
            }
        }

        return $mapped;
    }

    /**
     * @return MapperInterface[]
     * @throws BindingResolutionException
     */
    private function getMappers(): array
    {
        $categoryMapper = app()->make(
            CategoryMapper::class,
            [
                $this->feedMapTo->getCategoryMapping(),
                $this->feedMapTo->getWoocommerceCategories(),
            ]
        );

        $attributeMapper = app()->make(
            AttributeMapper::class,
            [
                $this->feedMapTo->getWoocommerceAttributes()->toArray(),
                $this->feedMapTo->getLocale(),
            ]
        );

        $priceMapper = app()->make(PriceMapper::class);

        return [
            $categoryMapper,
            $attributeMapper,
            $priceMapper,
            app()->make(
                RuleMapper::class,
                [
                    $attributeMapper,
                    $categoryMapper,

                    $this->feedMapTo->getRules(),
                ]
            ),
        ];
    }

    /**
     * @return InspireupliftProductBuilder
     * @throws BindingResolutionException
     */
    private function getProductBuilder(): InspireupliftProductBuilder
    {
        $builder = config(sprintf('product_builder.%s', $this->feedMapTo->getFeedSlug()),'default');

        return app()->make($builder, [$this->feedMapTo->getFeedMapping()]);
    }
}
