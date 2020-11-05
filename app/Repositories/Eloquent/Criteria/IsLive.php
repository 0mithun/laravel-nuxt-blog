<?php


namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\CriterianContract;

class IsLive implements CriterianContract
{
    public function apply($model)
    {
        return $model->where('is_live', true);
    }

}
