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
     * @param int $designId
     * @param array $data
     * @return mixed
     */
    public function addComment(int $designId, array  $data);

    /**
     * @param int $id
     * @return mixed
     */
    public function like(int $id);

    /**
     * @param int $designId
     * @return mixed
     */
    public function isLikedByUser(int $designId);
}
