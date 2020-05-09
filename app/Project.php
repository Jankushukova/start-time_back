<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'description',
        'deadline',
        'content',
        'video',
        'goal',
        'gathered',
        'active',
        'owner_id',
        'category_id',
    ];


    public function user()
    {
        return $this->belongsTo('App\User','owner_id');
    }

    public function backers(){
        return $this->belongsToMany('App\User', 'project_orders', 'project_id', 'user_id');
    }



    public function category(){
        return $this->belongsTo('App\ProjectCategory');
    }


    public function gifts(){
        return $this->hasMany('App\ProjectGift','project_id', 'id');
    }

    public function updates(){
        return $this->hasMany('App\Update');
    }

    public function images(){
        return $this->hasMany('App\ProjectImage');
    }

    public function comments(){
        return $this->hasMany('App\ProjectComment');
    }
    public function questions(){
        return $this->hasMany('App\ProjectQuestion');
    }

    public function likes(){
        return $this->hasMany('App\ProjectLike');
    }


}
