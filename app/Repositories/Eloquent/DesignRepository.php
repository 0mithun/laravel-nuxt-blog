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
     * @param int $designId
     * @param array $data
     * @return mixed
     */
    public function addComment(int $designId, array  $data){
        $design = $this->find($designId);
        $comment = $design->comments()->create($data);

        return  $comment;
    }

    /**
     * @param int $id
     * @return mixed
     */
    public function like(int $id){
        $design = $this->find($id);
        if($design->isLiked()){
            $design->unlike();
        }else{
            $design->like();
        }
    }

    /**
     * @param int $designId
     * @return mixed
     */

    public function isLikedByUser($designId){
        $design = $this->find($designId);

        return $design->isLiked();
    }
}
