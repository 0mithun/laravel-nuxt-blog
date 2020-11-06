<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Team;
use App\Repositories\Contracts\InvitationContract;
use App\Repositories\Contracts\TeamContract;
use App\Repositories\Contracts\UserContract;
use Illuminate\Http\Request;
use Mail;

class InvitationsController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(InvitationContract $invitations, TeamContract $teams, UserContract $users)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }

    public function invite(Request $request, $teamId){
        $team = $this->teams->find($teamId);
        $this->validate($request, [
            'email' =>['required','email']
        ]);

        $user = auth()->user();

        //Checks is authenticated user owned the team
        if(!$user->isOwnerOfTeam($team)){
            return response()->json(['email'=>'You are not the team owner'], 401);
        }

        //Check if the email has a pending invitations
        if($team->hasPendingInvite($request->email)){
            return response()->json(['email'=>'Email already has a pending invite'], 422);
        }

        //Get the recipient by email
        $recipient = $this->users->findByEmail($request->email);

        //if the recipient dose'nt exists send invitation to join the team
        if(! $recipient){
            $this->createInvitation(false, $team, $request->email);
            return response()->json(['message'=>'Invitation sent to user'], 200);
        }

        //Check the team has already the user

        if($team->hasUser($recipient)){
            return response()->json(['email'=>'This user seems to be a team member already.'], 422);
        }

        //Send invitation to the user
        $this->createInvitation(true, $team, $request->email);
        return response()->json(['message'=>'Invitation sent to user'], 200);

    }

    public function resend($id){
        $invitation = $this->invitations->find($id);

        //Checks is authenticated user owned the team
        if(!auth()->user()->isOwnerOfTeam($invitation->team)){
            return response()->json(['email'=>'You are not the team owner'], 401);
        }


        $recipient = $this->users->findByEmail($invitation->recipient_email);


        Mail::to($invitation->recipient_email)
            ->send(new SendInvitationToJoinTeam($invitation , !is_null($recipient)));

        return response()->json(['message'=>'Invitation resent'], 200);
    }

    public function respond(Request $request, $id){

    }

    public function destroy($id){

    }


    protected function createInvitation(bool $user_exists, Team $team, string $email){
        $invitation = $this->invitations->create([
            'team_id'    => $team->id,
            'sender_id'    => auth()->id(),
            'recipient_email'    => $email,
            'token'    => md5(uniqid(microtime())),
        ]);

        Mail::to($email)
            ->send(new SendInvitationToJoinTeam($invitation, $user_exists));
    }
}
