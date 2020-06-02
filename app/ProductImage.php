<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'image',
        'product_id'
    ];

    public function product(){
        return $this->belongsTo('App\Product');
    }
    public function getImageAttribute($key)
    {
        return asset($key);
    }
}
