<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectLike extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'viewed',
        'user_id',
        'project_id'
    ];

}
