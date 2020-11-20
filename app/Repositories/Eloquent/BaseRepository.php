<?php


namespace App\Repositories\Eloquent;


use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\BaseContract;
use App\Repositories\Criteria\CriteriaContract;
use Illuminate\Support\Arr;


/**
 * Class BaseRepository
 * @package App\Repositories\Eloquent
 */
abstract class BaseRepository implements BaseContract, CriteriaContract
{
    /**
     * @var mixed
     */
    protected $model;

    /**
     * BaseRepository constructor.
     * @throws ModelNotDefined
     */
    public function __construct()
    {
        $this->model = $this->getModelClass();
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return $this->model->get();
//        return $this->model::all();
    }


    /**
     * @return mixed
     * @throws ModelNotDefined
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getModelClass(){
        if(!method_exists($this, 'model')){
            throw new ModelNotDefined('No model defined');
        }
        return app()->make($this->model());
//        return $this->model();
    }

    /**
     * @param int $id
     * @return mixed|void
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * @param string $column
     * @param string $value
     * @return mixed|void
     */
    public function findWhere(string $column, string $value)
    {
        return $this->model->where($column, $value)->get();
    }

    /**
     * @param string $column
     * @param string $value
     * @return mixed|void
     */
    public function findWhereFirst(string $column, string $value)
    {
       return $this->model->where($column, $value)->firstOrFail();
    }

    /**
     * @param int $perPage
     * @return mixed|void
     */
    public function paginate(int $perPage = 10)
    {
        return $this->model->paginate($perPage);
    }

    /**
     * @param array $data
     * @return mixed|void
     */
    public function create(array $data)
    {
       return $this->model->create($data);
    }

    /**
     * @param int $id
     * @param array $data
     * @return mixed|void
     */
    public function update(int $id, array $data)
    {
        $record = $this->find($id);
            $record->update($data);
        return $record;
    }

    /**
     * @param $id
     * @return mixed|void
     */
    public function delete($id)
    {
       return $this->find($id)->delete();
    }

    /**
     * @param mixed ...$criteria
     * @return mixed|void
     */
    public function withCriteria(...$criteria)
    {
        $criteria = Arr::flatten($criteria);

        foreach ($criteria as $criterian){
            $this->model = $criterian->apply($this->model);
        }
        return $this;
    }


}
