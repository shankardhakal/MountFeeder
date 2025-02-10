<?php

declare(strict_types=1);

namespace App\Import\Mapper;

use App\Import\Resolver\Attributes;
use App\Woocommerce\WoocommerceProduct;

class AttributeMapper implements MapperInterface
{
    private const MULTI_ATTRIBUTE_PER_PRODUCT = [
        Attributes::ATTRIBUTE_COLOR,
        Attributes::ATTRIBUTE_SIZE,
    ];

    private const ATTRIBUTES_SEPARATORS = [
        ',',
        ';',
        '|',
    ];

    /**
     * @var array attributes from API
     */
    private array $woocommerceAttributes = [];

    /**
     * @var array mapped products attributes
     */
    private array $productAttributes = [];

    /**
     * @var array
     */
    private array $localeAttributesNameMap;

    /**
     * @var string
     */
    private string $locale;

    /**
     * AttributeMapper constructor.
     *
     * @param  array  $woocommerceAttributes
     * @param  string  $locale
     */
    public function __construct(array $woocommerceAttributes, string $locale)
    {
        $this->localeAttributesNameMap = Attributes::ATTRIBUTES_BY_LOCALE[$locale];
        $this->locale = $locale;

        foreach ($woocommerceAttributes as $attribute) {
            $this->woocommerceAttributes[strtolower($attribute['name'])] = $attribute;
        }
    }

    public function addColorAttribute($color)
    {
        $color = (array) $color;
        $this->productAttributes[] = [
            'visible' => true,
            'id'      => $this->getIdForAttribute($this->localeAttributesNameMap[Attributes::ATTRIBUTE_COLOR]),
            'options' => $color,
        ];
    }

    private function getIdForAttribute(string $name): ?int
    {
        return $this->woocommerceAttributes[strtolower($name)]['id'] ?? null;
    }

    public function addDeliveryCost($value)
    {
        if (empty($value)) {
            $value = Attributes::ZERO_DELIVERY_COST[$this->locale];
        }

        return $this->getAttributesFor($value, Attributes::ATTRIBUTE_DELIVERY_COST);
    }

    public function getAttributesFor(array $value, string $attributeName): array
    {
        if (empty($value)) {
            return [];
        }

        $attributeNameAtShop = array_change_key_case($this->localeAttributesNameMap, CASE_LOWER)[strtolower($attributeName)];

        $id = $this->getIdForAttribute($attributeNameAtShop);

        if (! empty($id)) {
            return [
                'visible' => true,
                'id'      => $id,
                'options' => $value,
            ];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getProductAttributes(): array
    {
        return $this->productAttributes;
    }

    /**
     * @param  array  $productAttributes
     */
    public function setProductAttributes(array $productAttributes): void
    {
        $this->productAttributes = $productAttributes;
    }

    public function map(WoocommerceProduct $product, array $originalData): ?WoocommerceProduct
    {
        $attributes = [];
        foreach (Attributes::PRODUCT_ATTRIBUTES as $attributeAsProperty => $attributeName) {
            $attributeValue = trim($product->$attributeAsProperty ?? '');
            if (! empty($attributeValue)) {
                foreach (self::ATTRIBUTES_SEPARATORS as $separator) {
                    if (stripos($attributeValue, $separator) !== false) {
                        $attributeValue = array_map('trim', explode($separator, $attributeValue));
                        break;
                    }
                }

                $attribute = $this->getAttributesFor((array) $attributeValue, $attributeName);

                if (! empty($attribute)) {
                    $attributes[] = $attribute;
                }
            }
        }

        if (! empty($attributes)) {
            $product->attributes = $attributes;
        }

        return $product;
    }
}
