<?php


namespace App\Repositories\Contracts;


use Illuminate\Http\Request;

interface UserContract extends BaseContract
{
    public function all();

    public function findByEmail(string  $email);

    public function search(Request $request);
}
