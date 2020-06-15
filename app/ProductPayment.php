<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPayment extends Model
{
    const EPAY = 2;
    const CLOUD = 3;
    const KASPI = 1;
    use SoftDeletes;
    protected $fillable = [
        'sum',
        'order_id',
        'type_id'
    ];

    public function order()
    {
        return $this->belongsTo('App\ProductOrder','order_id');
    }


    public function bank()
    {
        return $this->belongsTo('App\PaymentType','type_id');
    }
}
