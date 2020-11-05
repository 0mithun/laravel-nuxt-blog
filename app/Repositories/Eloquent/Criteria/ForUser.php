<?php


namespace App\Repositories\Eloquent\Criteria;


use App\Repositories\Criteria\CriterianContract;

/**
 * Class ForUser
 * @package App\Repositories\Eloquent\Criteria
 */
class ForUser implements CriterianContract
{
    /**
     * @var
     */
    protected  $user_id;

    /**
     * ForUser constructor.
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }


    /**
     * @param $model
     * @return mixed
     */
    public function apply($model)
    {
       return $model->where('user_id', $this->user_id);
    }

}
