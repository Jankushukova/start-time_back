<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectOrder extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'viewed',
        'project_id',
        'payment_id',
        'user_id'
    ];

    public function type(){
        return $this->belongsTo('App\PaymentType');
    }
    public function payments(){
        return $this->hasMany('App\Payment');
    }
}
