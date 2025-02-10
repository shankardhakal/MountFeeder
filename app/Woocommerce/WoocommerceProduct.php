<?php

declare(strict_types=1);

namespace App\Woocommerce;

use App\Import\Resolver\Attributes;
use App\Import\WebsiteLocale;

class WoocommerceProduct
{
    public const DEFAULT_ENDING_TEXT_PRODUCT_DESCRIPTION = [
        WebsiteLocale::LOCALE_nl_NL => 'Dit product is verkrijgbaar bij %s. Klik op meer informatie en bekijk het gehele assortiment.',
        WebsiteLocale::LOCALE_en_US => 'This product is available at %s. Click for more information to view the product.',
        WebsiteLocale::LOCALE_de_DE => 'Diese Produkt ist verfügbar bei %s. Klicken Sie auf \'mehr Info\' für unsere ganze Auswahl.',
        WebsiteLocale::LOCALE_fi_FI => 'Tämä tuote satavilla %s:ssa.',
    ];

    public const BUTTON_TEXT = [
        WebsiteLocale::LOCALE_nl_NL => 'Meer informatie',
        WebsiteLocale::LOCALE_en_US => 'More information',
        WebsiteLocale::LOCALE_de_DE => 'Mehr Info',
        WebsiteLocale::LOCALE_fi_FI => 'Siiry verkkokauppan',
    ];

    public const NAME = 'name';
    public const COLOR = 'color';
    public const BRAND = 'brand';
    public const STATUS = 'status';
    public const DESCRIPTION = 'description';
    public const SHORT_DESCRIPTION = 'shortDescription';
    public const SKU = 'sku';
    public const ALTERNATE_SKU = 'alternateSku';
    public const PRICE = 'price';
    public const OLD_PRICE = 'oldPrice';
    public const CATEGORIES = 'categories';
    public const EXTERNAL_URL = 'externalURL';
    public const IMAGE_URL = 'imageURL';
    public const MATERIAL = 'material';
    public const DELIVERY_COST = 'deliveryCost';
    public const DELIVERY_TIME = 'deliveryTime';
    public const LENGTH = 'length';
    public const WIDTH = 'width';
    public const HEIGHT = 'height';
    public const WEIGHT = 'weight';
    public const WEIGHT_UNIT = 'weightUnit';
    public const DIMENSION_UNIT = 'dimensionUnit';
    public const FEED_NAME = 'feedName';

    public const DEFAULT_WEIGHT_UNIT = 'kg';
    public const DEFAULT_DIMENSION_UNIT = 'cm';

    const SHOP_SLUG = 'shopSlug';
    const SIZE = 'size';
    const GENDER = 'gender';
    const CONTINENT = 'continent';
    const LAND = 'land';
    const REGION = 'region';
    const HOLIDAY_TYPE = 'holidayType';
    const TRANSPORT = 'transport';
    const ACCOMMODATION_TYPE = 'accommodationType';
    const DEPARTURE = 'departure';
    const DURATION = 'duration';
    const ACCOMMODATION = 'accommodation';
    const THEME = 'theme';
    const PLACE = 'place';
    const IMAGES = 'images';

    public static $exportDataMapping = [
        'name'             => 'name',
        'description'      => 'description',
        'shortDescription' => 'short_description',
        'sku'              => 'sku',
        'price'            => 'regular_price',
        'salePrice'        => 'sale_price',
        'categories'       => 'categories',
        'externalURL'      => 'external_url',
        'type'             => 'type',
        'buttonText'       => 'button_text',
        'status'           => 'status',
        'stockQuantity'    => 'stock_quantity',
        'weight'           => 'weight',
        'length'           => 'length',
        'width'            => 'width',
        'height'           => 'height',
        'attributes'       => 'attributes',
    ];

    public static $skipOnMapping = [
        'type'          => 'type',
        'buttonText'    => 'button_text',
        'status'        => 'status',
        'stockQuantity' => 'stock_quantity',
        'weight'        => 'weight',
        'length'        => 'length',
        'width'         => 'width',
        'height'        => 'height',
        'attributes'    => 'attributes',
    ];

    private const MANDATORY_FIELDS = [
        self::NAME,
        self::DESCRIPTION,
        self::SKU,
        self::PRICE,
        self::CATEGORIES,
        self::IMAGE_URL,
        self::EXTERNAL_URL,
    ];

    public $name;
    public $slug;
    public $status;
    public $description;
    public $oldPrice;
    public $shortDescription;
    public $sku;
    public $price;
    public $salePrice;
    public $stockQuantity;
    public $weight;
    public $length;
    public $width;
    public $height;
    public $attributes;
    public $categories;
    public $imagesURL;
    public $imageURL;
    public $externalURL;
    public $brand;
    public $color;
    public $shopSlug;
    public $dimensions;
    public string $type = 'external';
    public $buttonText;
    public $weightUnit;
    public $dimensionUnit;
    public string $locale;
    public string $feedName;

    /**
     * WoocommerceProduct constructor.
     * @param  array  $productData
     */
    public function __construct( array $productData)
    {
        foreach ($productData as $property=>$value){

            if(property_exists($this, $property)){
                $this->$property = $value;
            }
        }

        $this->locale = 'en_US';
        $this->buttonText = self::BUTTON_TEXT[$this->locale];
    }

    /**
     * @return array
     */
    public function getExportData(): array
    {
        $exportData = [];
        foreach (self::$exportDataMapping as $property => $woocommerceField) {
            $exportData[$woocommerceField] = $this->$property;
        }

        $imageUrl = $this->imageURL ?? $this->imagesURL ?? null;
        if (! empty($imageUrl)) {
            $exportData[self::IMAGES] = [['src' => $imageUrl]];
        }

        $exportData[self::DESCRIPTION] = $this->getFormattedDescription($exportData['description']);

        return array_filter($exportData);
    }

    /**
     * @return array
     */
    public static function getMappableFields(): array
    {
        $mappingFields = array_diff_assoc(self::$exportDataMapping, self::$skipOnMapping);
        $mappingFields['alternateSku'] = 'alternateSku';
        $mappingFields['deliveryCost'] = 'deliveryCost';
        $mappingFields['deliveryTime'] = 'deliveryTime';
        $mappingFields['extraImagesURL'] = 'extraImagesURL';
        $mappingFields['color'] = 'color';
        $mappingFields['brand'] = 'brand';
        $mappingFields['size'] = 'size';
        $mappingFields['gender'] = 'gender';
        $mappingFields['subCategory'] = 'subCategory';
        $mappingFields['imageURL'] = 'imageURL';
        $mappingFields['price'] = 'price';
        $mappingFields['oldPrice'] = 'oldPrice';
        $mappingFields['material'] = 'material';
        $mappingFields[Attributes::ATTRIBUTE_LAND] = Attributes::ATTRIBUTE_LAND;
        $mappingFields[Attributes::ATTRIBUTE_REGION] = Attributes::ATTRIBUTE_REGION;
        $mappingFields[Attributes::ATTRIBUTE_HOLIDAY_TYPES] = Attributes::ATTRIBUTE_HOLIDAY_TYPES;
        $mappingFields[Attributes::ATTRIBUTE_TRANSPORT] = Attributes::ATTRIBUTE_TRANSPORT;
        $mappingFields[Attributes::ATTRIBUTE_ACCOMMODATION_TYPE] = Attributes::ATTRIBUTE_ACCOMMODATION_TYPE;
        $mappingFields[Attributes::ATTRIBUTE_DEPARTURE] = Attributes::ATTRIBUTE_DEPARTURE;
        $mappingFields[Attributes::ATTRIBUTE_ACCOMMODATION] = Attributes::ATTRIBUTE_ACCOMMODATION;
        $mappingFields[Attributes::ATTRIBUTE_DURATION] = Attributes::ATTRIBUTE_DURATION;
        $mappingFields[Attributes::ATTRIBUTE_THEME] = Attributes::ATTRIBUTE_THEME;
        $mappingFields[Attributes::ATTRIBUTE_PLACE] = Attributes::ATTRIBUTE_PLACE;

        /* TODO fix this correctly later */
        unset($mappingFields['salePrice']);

        return $mappingFields;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        foreach (self::MANDATORY_FIELDS as $mandatoryField) {
            if (! isset($this->$mandatoryField) || empty($this->$mandatoryField)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  string  $productDesc
     * @return string
     */
    protected function getFormattedDescription(string $productDesc): string
    {
        $shopSpecificEndingText = sprintf(
            self::DEFAULT_ENDING_TEXT_PRODUCT_DESCRIPTION[$this->locale],
            $this->feedName
        );

        return str_replace(['!', '"'], ['', '\"'], "{$productDesc}<br>{$shopSpecificEndingText}</br>");
    }

    /**
     * @return string[]
     */
    public static function getExportDataMapping(): array
    {
        return self::$exportDataMapping;
    }

    /**
     * @param  string[]  $exportDataMapping
     */
    public static function setExportDataMapping(array $exportDataMapping): void
    {
        self::$exportDataMapping = $exportDataMapping;
    }

    /**
     * @return string[]
     */
    public static function getSkipOnMapping(): array
    {
        return self::$skipOnMapping;
    }

    /**
     * @param  string[]  $skipOnMapping
     */
    public static function setSkipOnMapping(array $skipOnMapping): void
    {
        self::$skipOnMapping = $skipOnMapping;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  mixed  $name
     * @return WoocommerceProduct
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param  mixed  $slug
     * @return WoocommerceProduct
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param  mixed  $status
     * @return WoocommerceProduct
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param  mixed  $shortDescription
     * @return WoocommerceProduct
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * @param  mixed  $sku
     * @return WoocommerceProduct
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param  mixed  $price
     * @return WoocommerceProduct
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSalePrice()
    {
        return $this->salePrice;
    }

    /**
     * @param  mixed  $salePrice
     * @return WoocommerceProduct
     */
    public function setSalePrice($salePrice)
    {
        $this->salePrice = $salePrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStockQuantity()
    {
        return $this->stockQuantity;
    }

    /**
     * @param  mixed  $stockQuantity
     * @return WoocommerceProduct
     */
    public function setStockQuantity($stockQuantity)
    {
        $this->stockQuantity = $stockQuantity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param  mixed  $weight
     * @return WoocommerceProduct
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param  mixed  $length
     * @return WoocommerceProduct
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param  mixed  $width
     * @return WoocommerceProduct
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param  mixed  $height
     * @return WoocommerceProduct
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param  mixed  $attributes
     * @return WoocommerceProduct
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param  mixed  $categories
     * @return WoocommerceProduct
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImagesURL()
    {
        return $this->imagesURL;
    }

    /**
     * @param  mixed  $imagesURL
     * @return WoocommerceProduct
     */
    public function setImagesURL($imagesURL)
    {
        $this->imagesURL = $imagesURL;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getImageURL()
    {
        return $this->imageURL;
    }

    /**
     * @param  mixed  $imageURL
     * @return WoocommerceProduct
     */
    public function setImageURL($imageURL)
    {
        $this->imageURL = $imageURL;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExternalURL()
    {
        return $this->externalURL;
    }

    /**
     * @param  mixed  $externalURL
     * @return WoocommerceProduct
     */
    public function setExternalURL($externalURL)
    {
        $this->externalURL = $externalURL;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @param  mixed  $brand
     * @return WoocommerceProduct
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param  mixed  $color
     * @return WoocommerceProduct
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShopSlug()
    {
        return $this->shopSlug;
    }

    /**
     * @param  mixed  $shopSlug
     * @return WoocommerceProduct
     */
    public function setShopSlug($shopSlug)
    {
        $this->shopSlug = $shopSlug;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDimensions()
    {
        return $this->dimensions;
    }

    /**
     * @param  mixed  $dimensions
     * @return WoocommerceProduct
     */
    public function setDimensions($dimensions)
    {
        $this->dimensions = $dimensions;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param  string  $type
     * @return WoocommerceProduct
     */
    public function setType(string $type): WoocommerceProduct
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getButtonText(): string
    {
        return $this->buttonText;
    }

    /**
     * @param  string  $buttonText
     * @return WoocommerceProduct
     */
    public function setButtonText(string $buttonText): WoocommerceProduct
    {
        $this->buttonText = $buttonText;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWeightUnit()
    {
        return $this->weightUnit;
    }

    /**
     * @param  mixed  $weightUnit
     * @return WoocommerceProduct
     */
    public function setWeightUnit($weightUnit)
    {
        $this->weightUnit = $weightUnit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDimensionUnit()
    {
        return $this->dimensionUnit;
    }

    /**
     * @param  mixed  $dimensionUnit
     * @return WoocommerceProduct
     */
    public function setDimensionUnit($dimensionUnit)
    {
        $this->dimensionUnit = $dimensionUnit;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param  string  $locale
     * @return WoocommerceProduct
     */
    public function setLocale(string $locale): WoocommerceProduct
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getFeedName(): string
    {
        return $this->feedName;
    }

    /**
     * @param  string  $feedName
     * @return WoocommerceProduct
     */
    public function setFeedName(string $feedName): WoocommerceProduct
    {
        $this->feedName = $feedName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    /**
     * @param  mixed  $oldPrice
     * @return WoocommerceProduct
     */
    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param  mixed  $description
     * @return WoocommerceProduct
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }



}
