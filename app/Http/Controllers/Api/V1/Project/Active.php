<?php


namespace App\Http\Controllers\Api\V1\Project;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class Active implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('active',1);
    }
}
