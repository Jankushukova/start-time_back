<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectCategory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name_rus',
        'name_kz',
        'name_eng'
    ];

    public function projects(){
        return $this->hasMany('App\Project', 'category_id');
    }
}
