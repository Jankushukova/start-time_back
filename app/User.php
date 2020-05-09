<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'phone_number',
        'image',
        'biography',
        'email',
        'role_id'=>Role::UNAUTHORIZED_CLIENT_ID,
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function isAdmin(){
        return $this->role_id == Role::ADMIN_ID;
    }




    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
       return [];
    }



    public function projects(){
        return $this->hasMany('App\Project', 'owner_id');    }

    public function followers(){
        return $this->belongsToMany('App\User', 'followers', 'following_id', 'follower_id');
    }

    public function followings(){
        return $this->belongsToMany('App\User', 'followers', 'follower_id', 'following_id');
    }

    public function baked(){
        return $this->belongsToMany('App\Project', 'project_orders', 'user_id', 'project_id');
    }
    public function payments(){
        return $this->belongsToMany('App\Payment', 'project_orders', 'user_id', 'payment_id');

    }


}
