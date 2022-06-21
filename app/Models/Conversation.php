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

        $conversation->createChatFromResponse($type);

        $conversation->load('chats');

        return $conversation;
    }

    public function createChatFromResponse($response){
        $chat = new Chat;
        $chat->conversation_id = $this->id;
        $chat->content = $response->content;
        $chat->type_id = $response->id;
        $chat->save();

        return $chat;
    }

    public function createChatFromOption($option){
        $chat = new Chat;
        $chat->user_input = true;
        $chat->conversation_id = $this->id;
        $chat->content = $option->content;
        $chat->save();

        return $chat;
    }

    public function interpretMessage($message){
        // Get last question
        $last = optional($this->chats()->lastQuestion()->first())->response;

        $chat = new Chat;
        $chat->user_input = true;
        $chat->conversation_id = $this->id;
        $chat->content = '<p>'.$message.'</p>';
        $chat->save();

        if($last){
            $response = new Chat;
            $response->conversation_id = $this->id;
            $response->content = '<p>Ja.. dit is awkward. Dit is een behoorlijk domme chatbot en kan nog helemaal geen tekst interpreteren. Hij kan wel de bovenste vraag nog eens herhalen :-)</p>';
            $response->save();

            // Repeat question
            $repeat = $this->createChatFromResponse($last);
        }

        return [
            'chat' => $chat,
            'responses' => [$response, $repeat]
        ];
    }
}
