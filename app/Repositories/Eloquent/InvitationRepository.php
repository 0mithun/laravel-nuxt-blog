<?php


namespace App\Repositories\Eloquent;


use App\Models\Invitation;
use App\Repositories\Contracts\InvitationContract;

class InvitationRepository extends BaseRepository implements InvitationContract
{
    public function model(){
        return Invitation::class;
    }
}
