<?php


namespace App\Repositories\Contracts;


interface CommentContract extends BaseContract
{

    public function like(int $id);

    public function checkIsLiked(int $id);
}
