<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class Conversation extends Model
{
    const TOKEN = 'daanbot_session';

    public $visible = ['chats'];
    public $with = ['chats'];

    public function chats(){
        return $this->hasMany(Chat::class);
    }

    public static function getOrCreate(){
        if($token = Session::get(self::TOKEN)){
            if($conversation = Conversation::where('token', $token)->first()){
                return $conversation;
            }
        }
        $token = Str::random(16);
        Session::put(self::TOKEN, $token);
        Session::save();

        $conversation = Conversation::createNew($token);

        return $conversation;
    }

    public static function createNew($token){
        $conversation = new Conversation;
        $conversation->token = $token;
        $conversation->save();

        $flow = Flow::first();
        $type = $flow->start;

        $conversation->createChatFromType($type);

        $conversation->load('chats');

        return $conversation;
    }

    public function createChatFromType($type){
        $chat = new Chat;
        $chat->conversation_id = $this->id;
        $chat->content = $type->content;
        $chat->type_id = $type->id;
        $chat->save();

        return $chat;
    }
}
