<?php

namespace App\Http\Controllers\Chats;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\ChatContract;
use App\Repositories\Contracts\MessageContract;
use App\Repositories\Eloquent\Criteria\WithTrashed;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $chats;
    protected $messages;


    public function __construct(ChatContract $chats, MessageContract $messages)
    {
        $this->chats = $chats;
        $this->messages = $messages;
    }

    //Send mesage to user
    public function sendMessage(Request $request){

        //Validate the request
        $this->validate($request, [
            'recipient'    => ['required'],
            'body'         => ['required'],
        ]);

        $recipient = $request->recipient;
        $user = auth()->user();
        $body = $request->body;


        //Check if there is an existing chat between auth user & the recipient
        $chat = $user->getChatWithUser($recipient);
        if(!$chat){
            $chat = $this->chats->create([]);
            $this->chats->createParticipants($chat->id, [$user->id, $recipient]);
        }

        //Add the message to the chat
        $message = $this->messages->create([
            'user_id'   => $user->id,
            'chat_id'   => $chat->id,
            'body'      => $body,
            'last_read' => null
        ]);

        return new MessageResource($message);

    }

    //Get chat for user
    public function getUserChat(){
        $chats = $this->chats->getUserChats();
        return ChatResource::collection($chats);
    }

    //Get mesages for chat
    public function getChatMesages($id){
//        $messages = $this->messages->findWhere('chat_id', $id);

        $messages = $this->messages->withCriteria([
            new WithTrashed(),
        ])->findWhere('chat_id', $id)->all();
        return MessageResource::collection($messages);
    }

    //Mark chat as read
    public function markAsRead($id){
        $chat = $this->chats->find($id);
        $chat->markAsReadForUser(auth()->id());

        return response()->json(['message'=>'success'], 200);
    }

    //Destroy message
    public function destroyMessages($id){
        $message = $this->messages->find($id);
        $this->authorize('delete', $message);
        $message->delete();

        return response()->json(['message'=>'success'], 200);
    }
}
