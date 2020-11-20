<?php


namespace App\Repositories\Criteria;


/**
 * Interface CriteriaContract
 * @package App\Repositories\Criteria
 */
interface CriteriaContract
{
    /**
     * @param mixed ...$criteria
     * @return mixed
     */
    public function withCriteria(...$criteria);
}
