<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsImage extends Model
{
    const PATH = 'images/news';

    protected $fillable = [
        'image',
        'news_id'
    ];
    public function getImageAttribute($key)
    {
        return asset($key);
    }
}
