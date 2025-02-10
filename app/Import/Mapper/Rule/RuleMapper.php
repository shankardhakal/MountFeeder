<?php

declare(strict_types=1);

namespace App\Import\Mapper\Rule;

use App\Import\Mapper\AttributeMapper;
use App\Import\Mapper\CategoryMapper;
use App\Import\Mapper\MapperInterface;
use App\ParseRules\ParserExpressionLanguageProvider;
use App\Woocommerce\WoocommerceProduct;
use Illuminate\Support\Collection;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class RuleMapper implements MapperInterface
{
    /**
     * @var ExpressionLanguage
     */
    protected ExpressionLanguage $expressionLanguage;

    // TODO refactor this to a service
    protected AttributeMapper $attributeMapper;

    // TODO refactor this to a service
    private CategoryMapper $categoryMapper;

    /**
     * @var Collection
     */
    private Collection $rules;

    private ?WoocommerceProduct $product;

    /**
     * RuleMapper constructor.
     * @param  ExpressionLanguage  $expressionLanguage
     * @param  AttributeMapper  $attributeMapper
     * @param  CategoryMapper  $categoryMapper
     * @param  Collection  $rules
     */
    public function __construct(
        ExpressionLanguage $expressionLanguage,
        AttributeMapper $attributeMapper,
        CategoryMapper $categoryMapper,
        Collection $rules
    ) {
        $this->expressionLanguage = $expressionLanguage;
        $this->attributeMapper = $attributeMapper;
        $this->categoryMapper = $categoryMapper;
        $this->rules = $rules;
    }

    public function map(WoocommerceProduct $product, array $originalData): ?WoocommerceProduct
    {
        $this->registerRules();
        $this->product = $product;
        foreach ($this->rules as $rule) {
            $this->expressionLanguage->evaluate($rule, ['product' => &$this->product, 'csvProduct' => $originalData]);

            if ($this->product === null) {
                return null;
            }
        }

        return $this->product;
    }

    public function registerRules()
    {
        // TODO find a better solution here
        $this->expressionLanguage = new ExpressionLanguage(null, [new ParserExpressionLanguageProvider()]);

        $this->expressionLanguage->register(
            'addAttributesFor',
            function ($str) {
                return '';
            },
            function ($arguments, $value, $attributeName) {
                $this->addAttributesFor($value, $attributeName, $this->product);
            }
        );

        $this->expressionLanguage->register(
            'addSubCategory',
            function ($str) {
                return '';
            },
            function ($arguments, $subCategory) {
                $this->addSubCategory($subCategory, $this->product);
            }
        );

        $this->expressionLanguage->register(
            'explodeAndSet',
            function ($str) {
                return '';
            },
            function ($arguments, $delimiter, $value, $attributeName) {
                $this->addAttributesFor(explode($delimiter, $value), $attributeName, $this->product);
            }
        );

        $this->expressionLanguage->register(
            'skipProduct',
            function ($str) {
                return '';
            },
            function (&$arguments) {
                $arguments['product'] = null;
                $this->product = null;
            }
        );
    }

    /**
     * Wrapper for expression language.
     * @param $value
     * @param  string|array  $attributeName
     * @param  WoocommerceProduct  $product
     */
    public function addAttributesFor(array $value, $attributeName, WoocommerceProduct &$product)
    {
        if (! is_array($product->attributes)) {
            $product->attributes = [];
        }

        $product->attributes[] = $this->attributeMapper->getAttributesFor($value, $attributeName);
    }

    /**
     * @param  string  $subCategory
     * @param  WoocommerceProduct  $product
     */
    public function addSubCategory(string $subCategory, WoocommerceProduct &$product)
    {
        $parentId = $product->categories[0]['id'] ?? null;

        $subCategoryId = $this->categoryMapper->getIdFromName($subCategory);

        if (! empty($subCategoryId) && ! empty($parentId)) {
            $product->categories[] = [
                'id'     => $subCategoryId,
                'parent' => $parentId,
                'name'   => $subCategory,
            ];
        }
    }
}
