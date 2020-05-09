<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsLike extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'viewed',
        'news_id',
        'user_id'
    ];


}
