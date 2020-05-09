<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductLike extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'viewed',
        'product_id',
        'user_id'
    ];
}
