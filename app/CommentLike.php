<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommentLike extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'viewed',
        'user_id',
        'project_comment_id',
        'news_comment_id'
    ];


}
