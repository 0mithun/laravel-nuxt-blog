<?php


namespace App\Repositories\Contracts;


interface BaseContract
{
    /**
     * @return mixed
     */
    public function all();


    /**
     * @param int $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */

    public function findWhere(string $column, string $value);


    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public function findWhereFirst(string $column, string $value);

    /**
     * @param int $perPage
     * @return mixed
     */
    public function paginate(int $perPage = 10);


    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);


    /**
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function update(int $id, array $data);


    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);

}
