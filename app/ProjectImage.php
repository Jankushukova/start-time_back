<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectImage extends Model
{
    const PATH = 'images/project';

    protected $fillable = [
        'image',
        'product_id'
    ];
}
