<?php

declare(strict_types=1);

namespace App\Import\Mapper;

use App\Woocommerce\WoocommerceProduct;
use Illuminate\Support\Collection;

class CategoryMapper implements MapperInterface
{
    private const POSSIBLE_CATEGORY_SEPARATORS = [
        '>',
        '|',
        ',',
    ];

    private Collection $categoryMapping;

    private Collection $woocommerceCategory;

    /**
     * CategoryMapper constructor.
     * @param  Collection  $categoryMapping
     * @param  Collection  $woocommerceCategory
     */
    public function __construct(Collection $categoryMapping, Collection $woocommerceCategory)
    {
        $this->categoryMapping = $categoryMapping;
        $this->woocommerceCategory = $woocommerceCategory->flatMap(
            function ($data) {
                return [$data['slug'] => $data];
            }
        );
    }

    /**
     * @param  WoocommerceProduct  $product
     * @param  array  $originalData
     * @return WoocommerceProduct|null
     */
    public function map(WoocommerceProduct $product, array $originalData): ?WoocommerceProduct
    {
        $categories = html_entity_decode($product->getCategories());

        $mappedCategory = $this->categoryMapping->get($categories);

        if (empty($mappedCategory)) {
            return null;
        }

        $woocommerceCategory = $this->woocommerceCategory->get($mappedCategory);

        if (! empty($woocommerceCategory)) {
            $product->sourceCategory = $product->categories;
            $product->targetCategory = $mappedCategory;

            $product->categories = [
                [
                    'id' => $woocommerceCategory['id'],
                ],
            ];

            return $product;
        }

        return null;
    }

    /**
     * @param  string  $categoryName
     *
     * @return array
     */
    public function getWebsiteCategory(string $categoryName): array
    {
        $categoryName = strtolower($categoryName);
        $mappedTo = $this->shopCategoryMapping[$categoryName] ?? null;

        if ($mappedTo === null) {
            return [null, null];
        }

        $categoryId = $this->getIdFromName($mappedTo);

        if ($categoryId === null) {
            return [null, null];
        }

        return [[['id' => $categoryId]], $mappedTo];
    }

    /**
     * @param  string  $categoryName
     * @return int|null
     */
    public function getIdFromName(string $categoryName): ?int
    {
        $resolvedCategory = $this->woocommerceCategory->get($categoryName);

        if (empty($resolvedCategory)) {
            $resolvedCategory = $this->woocommerceCategory->get(htmlentities($categoryName));
        }

        if (isset($resolvedCategory['id'])) {
            return (int) $resolvedCategory['id'];
        }

        return null;
    }
}
