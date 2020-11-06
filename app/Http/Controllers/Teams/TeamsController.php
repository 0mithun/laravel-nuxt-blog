<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\InvitationContract;
use App\Repositories\Contracts\TeamContract;
use App\Repositories\Contracts\UserContract;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamsController extends Controller
{
    protected $teams;
    protected $users;
    protected $invitations;


    public function __construct(TeamContract $teams, UserContract $users, InvitationContract $invitations)
    {
        $this->teams = $teams;
        $this->users = $users;
        $this->invitations = $invitations;
    }

    public function index(){
        $teams = $this->teams->withCriteria([

        ])->all();
        return TeamResource::collection($teams);
    }

    public function store(Request $request){
        $this->validate($request,[
            'name'      => ['required','string','max:80','unique:teams,name'],
        ]);

        //Create team in database
        $team = $this->teams->create([
                'owner_id'  => auth()->id(),
                'name' => $request->name,
                'slug'  => Str::slug($request->name),
            ]);

        return new TeamResource($team);

    }

    public function findById($id){
        $team = $this->teams->find($id);
        return new TeamResource($team);
    }

    public function findBySlug($id){

    }

    public function fetchUserTeams(){
        $teams = $this->teams->fetchUserTeams();

        return TeamResource::collection($teams);
    }

    public function update(Request $request, $id){
        $team = $this->teams->find($id);
        $this->authorize('update', $team);

        $this->validate($request, [
            'name'      => ['required','string','max:80','unique:teams,name,'.$team->id],
        ]);

        $team = $this->teams->update($id, [
            'name' => $request->name,
            'slug'  => Str::slug($request->name),
        ]);

        return new TeamResource($team);
    }

    public function destroy($id){
        $team = $this->teams->find($id);
        $this->authorize('delete', $team);

        $this->teams->delete($id);

        return response()->json(['message'=>'Success'], 200);
    }

    public function removeFromTeam($team_id, $user_id){
        $team = $this->teams->find($team_id);
        $user = $this->users->find($user_id);

        //check that the owner is not the team
        if( $user->isOwnerOfTeam($team)){
            return response()->json(['message'=>'You are owner of the team'], 401);
        }

        //check that the owner is not the team
        if( !auth()->user()->isOwnerOfTeam($team)){
            return response()->json(['message'=>'You are not owner of the team'], 401);
        }

        $this->invitations->removeUserFromTeam($team, $user_id);
        return response()->json(['message'=>'success'], 200);
    }
}
