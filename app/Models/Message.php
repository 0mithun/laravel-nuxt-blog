<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id','chat_id','body','last_read'
    ];

    protected $touches = [
        'chat'
    ];

    //Relationships for chat
    public function chat(){
        return $this->belongsTo(Chat::class);
    }

    //Relationships for sender
    public function sender(){
        return $this->belongsTo(User::class,'user_id');
    }



}
