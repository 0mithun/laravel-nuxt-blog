<?php


namespace App\Repositories\Eloquent;


use App\Models\User;
use App\Repositories\Contracts\UserContract;

class UserRepository implements UserContract
{
    public function all()
    {
       return User::all();
    }
}
