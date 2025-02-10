<?php

declare(strict_types=1);

namespace App\Import\Resolver;

use App\Import\WebsiteLocale;
use App\Woocommerce\WoocommerceProduct;

class Attributes
{
    public const ATTRIBUTE_BRAND = 'Brand';
    public const ATTRIBUTE_MATERIAL = 'Material';
    public const ATTRIBUTE_SELLER = 'Seller';
    public const ATTRIBUTE_DELIVERY_TIME = 'Delivery time';
    public const ATTRIBUTE_DELIVERY_COST = 'Delivery costs';
    public const ATTRIBUTE_COLOR = 'Color';
    public const ATTRIBUTE_LENGTH = 'Length';
    public const ATTRIBUTE_HEIGHT = 'Height';
    public const ATTRIBUTE_WIDTH = 'Width';
    public const ATTRIBUTE_SIZE = 'Size';
    public const ATTRIBUTE_GENDER = 'Gender';
    public const ATTRIBUTE_CONTINENT = 'Continent';
    public const ATTRIBUTE_LAND = 'Land';
    public const ATTRIBUTE_REGION = 'Region';
    public const ATTRIBUTE_HOLIDAY_TYPES = 'Holiday types';
    public const ATTRIBUTE_TRANSPORT = 'Transport';
    public const ATTRIBUTE_ACCOMMODATION_TYPE = 'Accommodation type';
    public const ATTRIBUTE_DEPARTURE = 'Departure';
    public const ATTRIBUTE_DURATION = 'Duration';
    public const ATTRIBUTE_ACCOMMODATION = 'Accommodation';
    public const ATTRIBUTE_THEME = 'Theme';
    public const ATTRIBUTE_PLACE = 'Place';

    public const PRODUCT_ATTRIBUTES = [
        WoocommerceProduct::BRAND              => self::ATTRIBUTE_BRAND,
        WoocommerceProduct::MATERIAL           => self::ATTRIBUTE_MATERIAL,
        WoocommerceProduct::SHOP_SLUG          => self::ATTRIBUTE_SELLER,
        WoocommerceProduct::DELIVERY_TIME      => self::ATTRIBUTE_DELIVERY_TIME,
        WoocommerceProduct::DELIVERY_COST      => self::ATTRIBUTE_DELIVERY_COST,
        WoocommerceProduct::COLOR              => self::ATTRIBUTE_COLOR,
        WoocommerceProduct::LENGTH             => self::ATTRIBUTE_LENGTH,
        WoocommerceProduct::HEIGHT             => self::ATTRIBUTE_HEIGHT,
        WoocommerceProduct::WIDTH              => self::ATTRIBUTE_WIDTH,
        WoocommerceProduct::SIZE               => self::ATTRIBUTE_SIZE,
        WoocommerceProduct::GENDER             => self::ATTRIBUTE_GENDER,
        WoocommerceProduct::CONTINENT          => self::ATTRIBUTE_CONTINENT,
        WoocommerceProduct::LAND               => self::ATTRIBUTE_LAND,
        WoocommerceProduct::REGION             => self::ATTRIBUTE_REGION,
        WoocommerceProduct::HOLIDAY_TYPE       => self::ATTRIBUTE_HOLIDAY_TYPES,
        WoocommerceProduct::TRANSPORT          => self::ATTRIBUTE_TRANSPORT,
        WoocommerceProduct::ACCOMMODATION_TYPE => self::ATTRIBUTE_ACCOMMODATION_TYPE,
        WoocommerceProduct::DEPARTURE          => self::ATTRIBUTE_DEPARTURE,
        WoocommerceProduct::DURATION           => self::ATTRIBUTE_DURATION,
        WoocommerceProduct::ACCOMMODATION      => self::ATTRIBUTE_ACCOMMODATION,
        WoocommerceProduct::THEME              => self::ATTRIBUTE_THEME,
        WoocommerceProduct::PLACE              => self::ATTRIBUTE_PLACE,
    ];

    public const ATTRIBUTES_BY_LOCALE = [
        WebsiteLocale::LOCALE_nl_NL => [
            self::ATTRIBUTE_BRAND              => 'Merk',
            self::ATTRIBUTE_MATERIAL           => 'Materiaal',
            self::ATTRIBUTE_SELLER             => 'Verkoper',
            self::ATTRIBUTE_DELIVERY_TIME      => 'Levertijd',
            self::ATTRIBUTE_DELIVERY_COST      => 'Verzendkosten',
            self::ATTRIBUTE_COLOR              => 'Kleur',
            self::ATTRIBUTE_LENGTH             => 'Breedte',
            self::ATTRIBUTE_HEIGHT             => 'Hoogte',
            self::ATTRIBUTE_WIDTH              => 'Lengte',
            self::ATTRIBUTE_SIZE               => 'Maat',
            self::ATTRIBUTE_GENDER             => 'Geslacht',
            self::ATTRIBUTE_CONTINENT          => 'Continent',
            self::ATTRIBUTE_LAND               => 'Land',
            self::ATTRIBUTE_REGION             => 'Regio',
            self::ATTRIBUTE_HOLIDAY_TYPES      => 'Soort vakantie',
            self::ATTRIBUTE_TRANSPORT          => 'Vervoer',
            self::ATTRIBUTE_ACCOMMODATION_TYPE => 'Accommodatie type',
            self::ATTRIBUTE_DEPARTURE          => 'Vertrek periode',
            self::ATTRIBUTE_DURATION           => 'Duur',
            self::ATTRIBUTE_ACCOMMODATION      => 'Thema\'s',
            self::ATTRIBUTE_THEME              => 'Theme',
            self::ATTRIBUTE_PLACE              => 'Plaats',
        ],
        WebsiteLocale::LOCALE_en_US => [
            self::ATTRIBUTE_BRAND              => 'Brand',
            self::ATTRIBUTE_MATERIAL           => 'Material',
            self::ATTRIBUTE_SELLER             => 'Seller',
            self::ATTRIBUTE_DELIVERY_TIME      => 'Delivery time',
            self::ATTRIBUTE_DELIVERY_COST      => 'Delivery costs',
            self::ATTRIBUTE_COLOR              => 'Color',
            self::ATTRIBUTE_LENGTH             => 'Length',
            self::ATTRIBUTE_HEIGHT             => 'Height',
            self::ATTRIBUTE_WIDTH              => 'Width',
            self::ATTRIBUTE_SIZE               => 'Measurement',
            self::ATTRIBUTE_GENDER             => 'Gender',
            self::ATTRIBUTE_CONTINENT          => 'Continent',
            self::ATTRIBUTE_LAND               => 'Land',
            self::ATTRIBUTE_REGION             => 'Region',
            self::ATTRIBUTE_HOLIDAY_TYPES      => 'Holiday types',
            self::ATTRIBUTE_TRANSPORT          => 'Transport',
            self::ATTRIBUTE_ACCOMMODATION_TYPE => 'Accommodation type',
            self::ATTRIBUTE_DEPARTURE          => 'Departure',
            self::ATTRIBUTE_DURATION           => 'Duration',
            self::ATTRIBUTE_ACCOMMODATION      => 'Accommodation',
            self::ATTRIBUTE_THEME              => 'Theme',
            self::ATTRIBUTE_PLACE              => 'Place',
        ],
        WebsiteLocale::LOCALE_de_DE => [
            self::ATTRIBUTE_BRAND              => 'Marke',
            self::ATTRIBUTE_MATERIAL           => 'Material',
            self::ATTRIBUTE_SELLER             => 'Verkaufer',
            self::ATTRIBUTE_DELIVERY_TIME      => 'Lieferzeit',
            self::ATTRIBUTE_DELIVERY_COST      => 'Versandkosten',
            self::ATTRIBUTE_COLOR              => 'Farbe',
            self::ATTRIBUTE_LENGTH             => 'Länge',
            self::ATTRIBUTE_HEIGHT             => 'Höhe',
            self::ATTRIBUTE_WIDTH              => 'Breite',
            self::ATTRIBUTE_SIZE               => 'Größe',
            self::ATTRIBUTE_GENDER             => 'Geschlecht',
            self::ATTRIBUTE_CONTINENT          => 'Kontinent',
            self::ATTRIBUTE_LAND               => 'Land',
            self::ATTRIBUTE_REGION             => 'Region',
            self::ATTRIBUTE_HOLIDAY_TYPES      => 'Verpflegung ',
            self::ATTRIBUTE_TRANSPORT          => 'Transport',
            self::ATTRIBUTE_ACCOMMODATION_TYPE => 'Hotelkategorie',
            self::ATTRIBUTE_DEPARTURE          => 'Abfahrt',
            self::ATTRIBUTE_DURATION           => 'Dauer',
            self::ATTRIBUTE_ACCOMMODATION      => 'Zimmertyp',
            self::ATTRIBUTE_THEME              => 'Reisethemen',
            self::ATTRIBUTE_PLACE              => 'Ort',
        ],

        WebsiteLocale::LOCALE_fi_FI => [
            self::ATTRIBUTE_BRAND              => 'Merkki',
            self::ATTRIBUTE_MATERIAL           => 'Materiali',
            self::ATTRIBUTE_SELLER             => 'Kauppa',
            self::ATTRIBUTE_DELIVERY_TIME      => 'Toimitus aika',
            self::ATTRIBUTE_DELIVERY_COST      => 'Toimitus hinta',
            self::ATTRIBUTE_COLOR              => 'Väri',
            self::ATTRIBUTE_LENGTH             => 'Pittus',
            self::ATTRIBUTE_HEIGHT             => 'Pittus',
            self::ATTRIBUTE_WIDTH              => 'Levys',
            self::ATTRIBUTE_SIZE               => 'Mittaus',
            self::ATTRIBUTE_GENDER             => 'Sukupuoli',
            self::ATTRIBUTE_CONTINENT          => 'Continent',
            self::ATTRIBUTE_LAND               => 'Land',
            self::ATTRIBUTE_REGION             => 'Region',
            self::ATTRIBUTE_HOLIDAY_TYPES      => 'Holiday types',
            self::ATTRIBUTE_TRANSPORT          => 'Transport',
            self::ATTRIBUTE_ACCOMMODATION_TYPE => 'Accommodation type',
            self::ATTRIBUTE_DEPARTURE          => 'Departure',
            self::ATTRIBUTE_DURATION           => 'Duration',
            self::ATTRIBUTE_ACCOMMODATION      => 'Accommodation',
            self::ATTRIBUTE_THEME              => 'Theme',
            self::ATTRIBUTE_PLACE              => 'Place',
        ],
    ];

    public const ZERO_DELIVERY_COST = [
        WebsiteLocale::LOCALE_nl_NL => 'Gratis verzending',
        WebsiteLocale::LOCALE_en_US => 'Free delivery',
        WebsiteLocale::LOCALE_de_DE => 'Kostenloser versand',
        WebsiteLocale::LOCALE_fi_FI => 'Ilmainen toimitus',
    ];

    public const ATTRIBUTE_ON_WEBSITE_CREATE = [
        WebsiteLocale::LOCALE_en_US => [
            'name' => 'Seller',
            'slug' => 'pa_product_site',
        ],
        WebsiteLocale::LOCALE_nl_NL => [
            'name' => 'Verkoper',
            'slug' => 'pa_verkoper',
        ],
        WebsiteLocale::LOCALE_de_DE => [
            'name' => 'Verkaufer',
            'slug' => 'pa_verkaufer',
        ],
        WebsiteLocale::LOCALE_fi_FI => [
            'name' => 'Kauppa',
            'slug' => 'pa_kauppa',
        ],
    ];
}
