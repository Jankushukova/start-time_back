<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Update extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title_eng',
        'title_rus',
        'title_kz',
        'description_eng',
        'description_rus',
        'description_kz',
        'project_id',
    ];


    public function project(){
        return $this->belongsTo('App\Project');
    }

    public function images(){
        return $this->hasMany('App\UpdateImage');
    }
}
