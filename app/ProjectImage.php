<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectImage extends Model
{
    const PATH = 'images/project';

    protected $fillable = [
        'image',
        'project_id'
    ];

    public function getImageAttribute($key)
    {
        return asset($key);
    }
}
