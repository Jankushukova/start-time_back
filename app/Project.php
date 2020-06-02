<?php

namespace App;

use App\Http\Controllers\Api\V1\Project\Active;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title_rus',
        'title_eng',
        'title_kz',
        'main_language',
        'description_rus',
        'description_kz',
        'description_eng',
        'deadline',
        'content_eng',
        'content_kz',
        'content_rus',
        'video',
        'goal',
        'gathered',
        'active',
        'owner_id',
        'category_id',
        'views'
    ];


    public function user()
    {
        return $this->belongsTo('App\User','owner_id');
    }

    public function bakers(){
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

    public function liked($id){
        $likes = ProjectLike::where('project_id',$this->id)->get();
        return $likes->contains('user_id',$id);
    }


//    protected function getArrayableAttributes()
//    {
//
//        foreach ($this->attributes as $key => $value) {
//            if ($key == 'deleted_at') continue;
//
//            if ( is_null($value) ) {
//                $this->attributes[$key] = '';
//            }
//        }
//
//        return $this->getArrayableItems($this->attributes);
//    }

    protected static function booted()
    {
        static::addGlobalScope(new Active());
    }

}
