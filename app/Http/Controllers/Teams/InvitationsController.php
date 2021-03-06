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
        $this->authorize('resend', $invitation);

        $recipient = $this->users->findByEmail($invitation->recipient_email);


        Mail::to($invitation->recipient_email)
            ->send(new SendInvitationToJoinTeam($invitation , !is_null($recipient)));

        return response()->json(['message'=>'Invitation resent'], 200);
    }

    public function respond(Request $request, $id){
        $invitation = $this->invitations->find($id);
        $this->authorize('respond', $invitation);

        $this->validate($request, [
            'token' => ['required'],
            'decision'  => ['required']
        ]);

        $token = $request->token;
        $decision = $request->decision;// 'accept' or 'deny'

        //Check to make sure that the invitation match
        if($invitation->token !== $token){
            return response()->json(['message'=> 'Invalid Token'], 401);
        }

        //Check if accept
        if($decision !== 'deny'){
            $this->invitations->addUserToTeam($invitation->team, auth()->id());
        }
        $invitation->delete();

        return response()->json(['message'=> 'Success'], 200);
    }

    public function destroy($id){
        $invitation = $this->invitations->find($id);
        $this->authorize('delete', $invitation);
        $invitation->delete();

        return response()->json(['message'=> 'Success'], 200);
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
