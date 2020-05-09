<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectCategory extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name'
    ];

    public function projects(){
        return $this->hasMany('App\Project', 'category_id');
    }
}
