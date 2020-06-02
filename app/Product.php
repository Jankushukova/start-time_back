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
        'views'

    ];

    public function user()
    {
        return $this->belongsTo('App\User','owner_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project','project_id');
    }
    public function likes(){
        return $this->hasMany('App\ProductLike');
    }



    public function ordered(){
        return $this->belongsToMany('App\User', 'product_orders', 'product_id', 'user_id');
    }

    public function images(){
        return $this->hasMany('App\ProductImage');
    }

    public function liked($id){
        $likes = ProductLike::where('product_id',$this->id)->get();
        return $likes->contains('user_id',$id);
    }

}
