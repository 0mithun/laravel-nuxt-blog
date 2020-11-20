<?php


namespace App\Repositories\Eloquent;


use App\Models\User;
use App\Repositories\Contracts\UserContract;

class UserRepository extends BaseRepository implements UserContract
{
    public function model(){
        return User::class;
    }

    public function findByEmail(string  $email){
        return $this->model->where('email', $email)->first();
    }
}
