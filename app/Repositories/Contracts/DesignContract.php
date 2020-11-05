<?php


namespace App\Repositories\Contracts;


/**
 * Interface DesignContract
 * @package App\Repositories\Contracts
 */
interface DesignContract extends BaseContract
{
    /**
     * @return mixed
     */
    public function all();


    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function applyTags(int $id, array $data);


    /**
     * @return mixed
     */
    public function allLive();
}
