<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $visible = ['id', 'pivot'];

    public function types(){
        return $this->belongsToMany(Type::class)->withPivot('label');
    }

    public function redirect(){
        return $this->belongsTo(Type::class, 'redirect_to');
    }

    public static function findOrCreate($node, $chat, $request){

        if(!$option = Option::find($node['real_id'])){
            $option = new Option;
        }
        $option->x = $node['x'];
        $option->y = $node['y'];

        // Attach redirect to
        $links = array_filter(
            $request->links,
            function($link) use ($node){ return $link['from'] === $node['id'];}
        ); // List of relevant links

        if(count($links) > 0){
            $redirects = array_filter($request->nodes, function($node) use ($links){ return $node['id'] === array_values($links)[0]['to'];});
            $redirect = array_values($redirects)[0];

            $type = Type::findOrCreate($redirect, $chat->flow, $request);

//            $type->x = $redirect['x'];
//            $type->y = $redirect['y'];
//            $type->save();
            $option->redirect_to = optional($type)->id;
        }

        $option->save();

        $option->types()->detach($chat);
        $option->types()->attach($chat, ['label' => $node['label']]);

        return $option;
    }
}
