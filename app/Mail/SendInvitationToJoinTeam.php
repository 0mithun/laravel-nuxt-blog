<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvitationToJoinTeam extends Mailable
{
    use Queueable, SerializesModels;

    public $inviation;
    public $user_exists;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation, bool $user_exists)
    {
        $this->inviation = $invitation;
        $this->user_exists = $user_exists;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->user_exists){
            $url = config('app.client_url').'/settings/teams';
            return $this->markdown('emails.invitations.invite-existing-user')
                ->subject('Invitation to join team '.$this->inviation->team->name)
                ->with(['invitation'=> $this->inviation, 'url'=> $url]);
        }else{
            $url = config('app.client_url').'/register?invitation='.$this->inviation->recipient_email;
            return $this->markdown('emails.invitations.invite-new-user')
                ->subject('Invitation to join team '.$this->inviation->team->name)
                ->with(['invitation'=> $this->inviation, 'url'=> $url]);
        }


    }
}
