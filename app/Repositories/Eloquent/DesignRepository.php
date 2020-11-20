<?php


namespace App\Repositories\Eloquent;


use App\Models\Design;
use App\Repositories\Contracts\DesignContract;
use Illuminate\Http\Request;

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

    public function search(Request $request)
    {
        $query = (new $this->model)->newQuery();
        $query->where('is_live', true);

        //Return only designs with comments
        if($request->has_comment){
            $query->has('comments');
        }

        //Return only designs assigned to teams
        if($request->has_team){
            $query->has('team');
        }

        //Search title & description for provided string
         if($request->q){
             $query->where(function($q) use($request){
                    $q->where('title','LIKE',"%{$request->q}%")
                        ->orWhere('description','LIKE',"%{$request->q}%");
             });
         }

         //Order the query by likes or latest first
        if($request->orderBy=='likes'){
            $query->withCount('likes');
            $query->orderByDesc('likes_count');
        }else{
            $query->latest();
        }


        return $query->get();
    }


}
