<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    public function chats(){
        return $this->hasMany(Chat::class, 'type');
    }

    public function flow(){
        return $this->belongsTo(Flow::class);
    }

    public function options(){
        return $this->belongsToMany(Option::class)->withPivot('label');
    }

    public static function findOrCreate($node, $flow, $request){
        if(!$chat = self::find($node['real_id'])){
            $chat = new self;
        }
        $chat->flow_id = $flow->id;
        $chat->label = $node['label'];
        $chat->content = $node['label'];
        $chat->x = $node['x'];
        $chat->y = $node['y'];
        $chat->save();

        // Delete nodes that were not given
        $options = array_map(function($n){return $n['real_id'];}, array_filter(
            $request->nodes,
            function ($n) use ($request, $node){ return $n['type'] === 'Option';}));
        $chat->options()->whereNotIn('options.id', $options)->delete();

        // Find options
        $options = array_filter(
            $request->nodes,
            function ($n) use ($request, $node){
                // Pak alle links waar de from de bovenstaande chat is
                $links = array_filter($request->links, function($link) use ($node){
                    return $link['from'] === $node['id'];
                });
                // En pak vervolgends de to ids
                $tos = array_map(function($link){
                    return $link['to'];
                }, $links);
                // Return alleen deze node als id in tos zit
                return in_array($n['id'], $tos);
            });

        foreach($options as $option){
            Option::findOrCreate($option, $chat, $request);
        }

        return $chat;
    }
}
