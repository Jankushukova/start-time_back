<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectQuestion extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'question',
        'user_id',
        'project_id',
        'viewed',
        'answer'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Project','project_id');
    }



}
