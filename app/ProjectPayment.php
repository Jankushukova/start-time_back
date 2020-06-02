<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectPayment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'sum',
        'viewed',
        'check_image',
        'order_id',
        'type_id'
    ];

    public function order()
    {
        return $this->belongsTo('App\ProjectOrder','order_id');
    }


    public function bank()
    {
        return $this->belongsTo('App\PaymentType','type_id');
    }
}
