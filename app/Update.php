<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Update extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'project_id',
    ];


    public function project(){
        return $this->belongsTo('App\Project');
    }

    public function images(){
        return $this->hasMany('App\UpdateImage');
    }
}
