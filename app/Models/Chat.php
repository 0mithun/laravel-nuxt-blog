<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Chat
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $latest_message
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat query()
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Chat whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Chat extends Model
{

    //relationships for participants
    public function participants(){
        return $this->belongsToMany(User::class,'participants');
    }

    //Relationships for messages
    public function messages(){
        return $this->hasMany(Message::class);
    }


    //Helper
    public function getLatestMessageAttribute(){
        return $this->messages()->latest()->first();
    }

    public function isUnreadForUser($user_id){
        return (bool) $this->messages()->whereNull('last_read')->where('user_id', '<>', $user_id )->count();
    }

    public function markAsReadForUser($user_id){
        $this->messages()->whereNull('last_read')->where('user_id','<>',$user_id)->update([
            'last_read'=> Carbon::now(),
        ]);
    }

}
