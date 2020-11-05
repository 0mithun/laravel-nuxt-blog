<?php


namespace App\Repositories\Eloquent;


use App\Models\Design;
use App\Repositories\Contracts\DesignContract;

class DesignRepository extends BaseRepository implements DesignContract
{
    public function model(){
        return Design::class;
    }

    /**
     * @param int $id
     * @param array $data
     */

    public function applyTags(int $id, array  $data){
        $this->find($id)->retag($data);
    }

    /**
     * @return mixed
     */
    public function allLive(){
        return $this->model->where('is_live', true)->get();
    }
}
