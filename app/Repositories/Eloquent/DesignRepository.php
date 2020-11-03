<?php


namespace App\Repositories\Eloquent;


use App\Models\Design;
use App\Repositories\Contracts\DesignContract;

class DesignRepository implements DesignContract
{

    public function all()
    {
       return Design::all();
    }
}
