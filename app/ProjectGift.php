<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectGift extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'description',
        'sum',
        'project_id'
    ];


    public function project(){
        return $this->belongsTo('App/Project');
    }
}
