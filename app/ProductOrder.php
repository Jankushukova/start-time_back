<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOrder extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'address',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'confirmed',
        'user_id',
        'confirmed'
    ];

    public function payments(){
        return $this->hasMany('App\ProjectPayment');
    }

    public function products(){
        return $this->hasMany('App\OrderProduct','order_id')->with('product');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function payment(){
        return $this->hasOne('App\ProductPayment', 'order_id')->with('bank');
}


}
