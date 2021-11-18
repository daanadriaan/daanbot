<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $visible = ['id', 'pivot'];
    public $appends = ['redirect'];

    public function types(){
        return $this->belongsToMany(Type::class)->withPivot('label');
    }
    public function getRedirectAttribute(){
        return $this->redirects()->inRandomOrder()->first();
    }
    public function redirects(){
        return $this->belongsToMany(Type::class, 'redirects', 'option_id', 'type_id');
    }

    public static function findOrCreate($node, $chat, $request){

        if(!$option = Option::find($node['real_id'])){
            $option = new Option;
        }
        $option->x = $node['x'];
        $option->y = $node['y'];

        // Attach redirect to
        $links = array_map(function($link){
                return $link['to'];
            }, array_filter(
                $request->links,
                function($link) use ($node){
                    return $link['from'] === $node['id'];
                }
            )
        ); // List of relevant links

        \Log::debug($links);

        $option->redirects()->detach();
        if(count($links) > 0){
            $redirects = array_filter($request->nodes, function($node) use ($links){
                return in_array($node['id'], $links);
            });

            \Log::debug($redirects);
            foreach(array_values($redirects) as $redirect){
                $type = Type::findOrCreate($redirect, $chat->flow, $request);
                $option->redirects()->attach($type);
            }
        }

        $option->save();

        $option->types()->detach($chat);
        $option->types()->attach($chat, ['label' => $node['label']]);

        return $option;
    }
}
