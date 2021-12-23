<?php
namespace App\Http\Controllers;

use App\Models\Steps\Option;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BotController extends Controller
{
    public function get(Request $request){
        return view('welcome');
    }

    public function conversation(Request $request){
        return response()->json($request->conversation);
    }

    public function choose(Request $request){
        // TODO: validate

        if($option = Option::find($request->option)){
            // Does this option redirect to flow?
            $redirect = optional(optional($option->redirect)->redirect)->start;

            if(!$redirect){
                // Does this option then redirect to answer?
                $redirect = $option->redirect;
            }
            if($redirect){
                $choice = $request->conversation->createChatFromOption($option);
                $chat = $request->conversation->createChatFromResponse($redirect);

                return response()->json(['chats' => [$chat]]);
            }
        }

        return response()->json('Error', 422);
    }

    public function interpret(Request $request){
        // TODO: interpret
        // TODO: validate

        $chats = $request->conversation->interpretMessage($request->message);

        return response()->json(['chats' => $chats]);

//        return response()->json('Error', 422);
    }
}
