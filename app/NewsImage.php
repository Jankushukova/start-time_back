<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsImage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'image',
        'news_id'
    ];
}
