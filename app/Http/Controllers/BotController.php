<?php

namespace App\Http\Controllers;

use App\Models\Flow;
use App\Models\Option;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BotController extends Controller
{
    public function maker(){
        return view('dashboard');
    }
    public function flows(Request $request){
        return response()->json(Flow::all());
    }
    public function flow(Request $request, Flow $flow){
        $flow = $flow->toNodes();
        return response()->json($flow);
    }
    public function storeFlow(Request $request, Flow $flow){
        $flow = $flow->storeFromNodes($request);
        return response()->json($flow);
    }
    public function newChat(Request $request, Flow $flow){
        $chat = new Type;
        $chat->flow_id = $flow->id;
        $chat->x = $request->x;
        $chat->y = $request->y;
        $chat->label = $request->label;
        //$chat->content = $request->content;
        $chat->save();

        return $chat;
    }
    public function newOption(Request $request, Flow $flow){
        $option = new Option;
        $option->x = $request->x;
        $option->y = $request->y;
        $option->save();

        return $option;
    }

    /* -- */

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
