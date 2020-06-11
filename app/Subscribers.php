<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscribers extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'email',
        'active'
    ];
}
