<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserContract;
use App\Repositories\Eloquent\Criteria\EagerLoad;
use Illuminate\Http\Request;
use function GuzzleHttp\Promise\all;

class UserController extends Controller
{
    protected $users;

    public function __construct(UserContract $users)
    {
        $this->users = $users;
    }

    public function index(){
        $users = $this->users->withCriteria([
            new EagerLoad(['designs']),
        ])->all();
        return UserResource::collection($users);
    }
    public function search(Request $request){
        $designers = $this->users->search($request);

        return UserResource::collection($designers);
    }
}
