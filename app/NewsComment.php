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


    public function likes(){
        return $this->hasMany('App\CommentLike','news_comment_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
    public function news()
    {
        return $this->belongsTo('App\News','news_id');
    }

    public function liked($id){
        $likes = CommentLike::where('news_comment_id',$this->id)->get();
        return $likes->contains('user_id',$id);
    }


}
