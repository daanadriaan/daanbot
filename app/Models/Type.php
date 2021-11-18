<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $visible = ['id', 'pivot'];

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
        $chat->save();

        // Delete options that were but no longer are connected to chat
        $options = array_map(function($n){return $n['real_id'];}, array_filter(
            $request->nodes,
            function ($n) use ($request, $node){ return $n['type'] === 'Option';}));
        $chat->options()->whereNotIn('options.id', $options)->delete();

        $options = [];
        foreach($request->nodes as $n){
            // Pak alle links waar de from de bovenstaande chat is
            $links = [];
            foreach($request->links as $key => $link){
                if($link['from'] === $node['id']){
                    $links[] = $link;
                }
            }

            // En pak vervolgens de to ids
            $tos = array_map(function($link){
                return $link['to'];
            }, $links);

            // Return alleen deze node als id in tos zit
            if(in_array($n['id'], $tos)){
                $options[] = $n;
            }
        }

        $request->links = array_filter($request->links, function($link) use ($node){
            return $link['from'] !== $node['id'];
        });

        foreach($options as $option){
            Option::findOrCreate($option, $chat, $request);
        }

        return $chat;
    }
}
