<?php

declare(strict_types=1);

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteConfiguration extends Model
{
    protected $fillable = ['locale', 'country'];
}
