<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'sum',
        'viewed',
        'check_image',
        'project_id',
        'type_id'
    ];

    public function project(){
        return $this->belongsTo('App\ProjectOrder');
    }

    public function product(){
        return $this->belongsTo('App\ProductOrder');

    }
}
