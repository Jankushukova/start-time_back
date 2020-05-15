<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'content',
        'views'
    ];

    public function likes(){
        return $this->hasMany('App\NewsLike', 'news_id');
    }

    public function comments(){
        return $this->hasMany('App\NewsComment', 'news_id');
    }

    public function images(){
        return $this->hasMany('App\NewsImage', 'news_id');
    }

    public function liked($id){
        $likes = NewsLike::where('news_id',$this->id)->get();
        return $likes->contains('user_id',$id);
    }
}
