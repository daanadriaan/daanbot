<?php

namespace App\Http\Controllers;

use App\Models\Flow;
use App\Models\Option;
use App\Models\Type;
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
        // TODO: validation

        if($option = Option::find($request->option)){
            if($redirect = $option->redirect){
                $chat = $request->conversation->createChatFromType($redirect);

                return response()->json(['chats' => [$chat]]);
            }
        }

        return response()->json('Error', 422);
    }
}
