<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsComment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'description',
        'viewed',
        'user_id',
        'news_id'
    ];


}
