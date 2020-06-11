<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UpdateImage extends Model
{
    const PATH = 'images/update';
    use SoftDeletes;
    protected $fillable = [
        'image',
        'update_id'
    ];

    public function getImageAttribute($key)
    {
        return asset($key);
    }
}
