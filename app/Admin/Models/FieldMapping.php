<?php

declare(strict_types=1);

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class FieldMapping extends Model
{
    public const FIELD_WOOCOMMERCE_FIELD = 'woocommerce_field';
    public const FIELD_SOURCE_FIELD = 'source_field';
    public const FIELD_NETWORK_ID = 'network_id';
    public const FIELD_FEED_ID = 'feed_id';

    /**
     * @var string[]
     */
    protected $fillable = [
        self::FIELD_WOOCOMMERCE_FIELD,
        self::FIELD_SOURCE_FIELD,
        self::FIELD_NETWORK_ID,
        self::FIELD_FEED_ID,
    ];
}
