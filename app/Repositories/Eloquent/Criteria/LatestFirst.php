<?php


namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\CriterianContract;

class LatestFirst implements CriterianContract
{
    public function apply($model)
    {
       return $model->latest();
    }

}
