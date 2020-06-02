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
        'active',
        'payment_id',
        'user_id'
    ];

    public function payments(){
        return $this->hasMany('App\ProjectPayment');
    }


}
