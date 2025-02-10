<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @deprecated
 */
class FeedImportLog extends Model
{
    const DEBUG = 'DEBUG';
    const INFO = 'INFO';
    const NOTICE = 'NOTICE';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';
    const ALERT = 'ALERT';
    const EMERGENCY = 'EMERGENCY';
    public static $levels = [
        'DEBUG',
        'INFO',
        'NOTICE',
        'WARNING',
        'ERROR',
        'CRITICAL',
        'ALERT',
        'EMERGENCY',
    ];
    protected $fillable = ['log_type', 'message', 'feed_id'];
}
