<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryMapping extends Model
{
    protected $fillable = ['woocommerce_category', 'feed_category'];
}
