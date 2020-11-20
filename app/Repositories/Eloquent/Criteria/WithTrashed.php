<?php


namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\CriterianContract;

class WithTrashed implements CriterianContract
{
    public function apply($model)
    {
        return $model->withTrashed();
    }

}
