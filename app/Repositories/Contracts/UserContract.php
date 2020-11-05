<?php


namespace App\Repositories\Contracts;


interface UserContract extends BaseContract
{
    public function all();

    public function findByEmail(string  $email);
}
