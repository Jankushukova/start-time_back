<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    const ADMIN_ID = 1;
    const DIRECTOR_ID = 2;
    const MANAGER_ID = 3;
    const AUTHORIZED_CLIENT_ID = 4;
    const UNAUTHORIZED_CLIENT_ID = 5;

    protected $fillable = [
        'name'
    ];
}
