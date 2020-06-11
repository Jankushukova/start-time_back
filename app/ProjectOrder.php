<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectOrder extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'firstname',
        'lastname',
        'confirmed',
        'phone_number',
        'email',
        'viewed',
        'project_id',
        'user_id',
        'gift_id'
    ];

    public function type(){
        return $this->belongsTo('App\PaymentType');
    }

    public function user(){
        return $this->hasMany('App\User','id', 'user_id');
    }
    public function gift(){
        return $this->belongsTo('App\ProjectGift', 'gift_id');
    }


    public function project(){
        return $this->belongsTo('App\Project', 'project_id');
    }



}
