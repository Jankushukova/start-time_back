<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProjectComment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'text',
        'user_id',
        'project_id',
        'viewed',

    ];

    public function likes(){
        return $this->hasMany('App\CommentLike','project_comment_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
    public function project()
    {
        return $this->belongsTo('App\Project','project_id');
    }

    public function liked($id){
        $likes = CommentLike::where('project_comment_id',$this->id)->get();
        return $likes->contains('user_id',$id);
    }
}
