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
        'viewed'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
