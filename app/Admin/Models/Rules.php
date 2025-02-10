<?php

declare(strict_types=1);

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rules extends Model
{
    public const FIELD_SYNTAX = 'syntax';
    public const FIELD_FEED_ID = 'feed_id';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_RAW_SYNTAX = 'raw_syntax';

    protected $fillable = [
        self::FIELD_SYNTAX,
        self::FIELD_FEED_ID,
        self::FIELD_DESCRIPTION,
        self::FIELD_RAW_SYNTAX,
    ];

    /**
     * @return BelongsTo
     */
    public function feed()
    {
        return $this->belongsTo(Feed::class);
    }
}
