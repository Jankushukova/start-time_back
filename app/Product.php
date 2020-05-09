<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'cost',
        'category_id'
    ];

    public function likes(){
        return $this->hasMany('App\ProductLike');
    }

    public function category(){
        return $this->belongsTo('App\ProductCategory');
    }

    public function ordered(){
        return $this->belongsToMany('App\User', 'product_orders', 'product_id', 'user_id');
    }

    public function images(){
        return $this->hasMany('App\ProductImage');
    }

}
