<?php

declare(strict_types=1);

namespace App\Admin\Models;

use App\Import\Resolver\Attributes;
use App\Traits\Encryptable;
use App\Woocommerce\WoocommerceApi;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Website extends Model
{
    use Encryptable;

    public const FIELD_NAME = 'name';
    public const FIELD_URL = 'url';
    public const FIELD_API_KEY = 'api_key';
    public const FIELD_API_SECRET = 'api_secret';
    public const FIELD_STATUS = 'status';
    public const FIELD_CONFIGURATION_ID = 'configuration_id';

    protected $fillable = [
        self::FIELD_NAME,
        self::FIELD_URL,
        self::FIELD_API_KEY,
        self::FIELD_API_SECRET,
        self::FIELD_STATUS,
        self::FIELD_CONFIGURATION_ID,
    ];

    protected $encryptable = [
        self::FIELD_API_KEY,
        self::FIELD_API_SECRET,
    ];

    public static function boot()
    {
        parent::boot();

        parent::created(
            function (self $website) {
                /** @var WoocommerceApi $woocommerceApi */
                $woocommerceApi = app(WoocommerceApi::class, [$website]);

                $shopAttribute = Attributes::ATTRIBUTE_ON_WEBSITE_CREATE[$website->configuration->locale];

                try {
                    $woocommerceApi->post(WoocommerceApi::ENDPOINT_PRODUCT_ATTRIBUTES, $shopAttribute);
                } catch (Exception $exception) {
                    Log::error('WC_ERROR_CREATING_SHOP_ATTRIBUTE', ['message' => $exception->getMessage()]);
                }
            }
        );
    }

    public function feeds()
    {
        return $this->hasMany(Feed::class, 'website_id', 'id');
    }

    public function configuration()
    {
        return $this->hasOne(WebsiteConfiguration::class, 'id', 'configuration_id');
    }
}
