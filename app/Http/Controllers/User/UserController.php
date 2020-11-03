<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\Contracts\UserContract;
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
        return UserResource::collection($this->users->all());
    }
}
