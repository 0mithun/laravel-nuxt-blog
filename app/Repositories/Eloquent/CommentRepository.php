<?php


namespace App\Repositories\Eloquent;


use App\Models\Comment;
use App\Repositories\Contracts\CommentContract;

class CommentRepository extends BaseRepository implements CommentContract
{
    public function model(){
        return Comment::class;
    }

    public function like(int $id){
        $comment = $this->find($id);
        if($comment->isLiked()){
            $comment->unlike();
        }else{
            $comment->like();
        }
    }

    public function checkIsLiked(int $id){
        $comment = $this->find($id);
        return $comment->isLiked();
    }
}
