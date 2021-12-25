<?php

namespace App\Http\Controllers;

use App\Models\Flow;
use App\Models\Option;
use App\Models\Step;
use App\Models\Steps\Response;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MakerController extends Controller
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
    public function updateFlow(Request $request, Flow $flow){
        if($request->flow['name'] == null || $request->flow['name'] == ""){
            $flow->delete();
            return response()->json(true);
        }
        $flow->name = $request->flow['name'];
        $flow->save();
        return response()->json($flow);
    }
    public function newFlow(Request $request){
        $flow = new Flow;
        $flow->name = "Nieuw";
        $flow->save();
        return response()->json($flow);
    }
    public function newChat(Request $request, Flow $flow){

        $chat = new Step;
        $chat->flow_id = $flow->id;
        $chat->x = $request->x;
        $chat->y = $request->y;
        $chat->type = $request->type;
        $chat->label = $request->label;
        $chat->redirect_flow_id =optional($request->redirect)->id;
        //$chat->content = $request->content;
        $chat->save();

        return $chat;
    }
}
