<?php


namespace App\Repositories\Eloquent\Criteria;




use App\Repositories\Criteria\CriterianContract;

class EagerLoad implements CriterianContract
{
    protected $relationships;

    public function __construct($relationships)
    {
        $this->relationships = $relationships;
    }


    public function apply($model)
    {
        return $model->with($this->relationships);
    }
}
