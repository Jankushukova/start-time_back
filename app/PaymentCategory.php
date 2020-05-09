<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentCategory extends Model
{
    const PRODUCT = 1;
    const PROJECT = 2;
    use SoftDeletes;
    protected $fillable = [
        'name'
    ];
}
