<?php


namespace App\Repositories\Contracts;


interface ChatContract extends  BaseContract
{

    public function createParticipants(int $chatId, array $data);

    public function getUserChats();
}
