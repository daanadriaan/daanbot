<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Flow extends Model
{
    protected $casts = [
        'meta' => 'object',
    ];

    public function types(){
        return $this->hasMany(Type::class);
    }

    public function start(){
        return $this->belongsTo(Type::class, 'type_id');
    }

    public function toNodes(){
        $flow =  [
            'centerX' =>  optional($this->meta)->centerX ?? 300,
            'centerY' => optional($this->meta)->centerY ?? 300,
            'scale' => optional($this->meta)->scale ?? 0,
            'nodes' => [],
            'links' => []
        ];

        foreach($this->types as $type){
            $node = rand(0, 10000000)+ $type->id;
            $flow['nodes'][] = [
                'id' => $node,
                'real_id' => $type->id,
                'label' => $type->label,
                'type' => 'Chat',
                'x' => $type->x,
                'y' => $type->y,
            ];
            foreach($type->options as $option){
                $id = rand(0, 100000000)+ $option->id;
                $flow['nodes'][] = [
                    'id' => $id,
                    'real_id' => $option->id,
                    'label' => $option->pivot->label,
                    'type' => 'Option',
                    'x' => $option->x,
                    'y' => $option->y,
                ];
                $flow['links'][] = [
                    'id' => rand(0, 100000000),
                    'from' => $node,
                    'to' => $id,
                ];
            }
        }

        // Create additional links
        foreach($this->types as $type){
            foreach($type->options as $option){
                $from = array_values(array_filter($flow['nodes'], function($node) use ($option){
                    return $node['type'] === 'Option' && $node['real_id'] === $option->id;
                }));
                if(count($from) > 0){
                    foreach($option->redirects as $key => $redirect){
                        $to = array_values(array_filter($flow['nodes'], function ($node) use ($redirect){
                            return $node['type'] === 'Chat' && $node['real_id'] === $redirect->id;
                        }));
                        if(count($to) > 0){
                            $flow['links'][] = ['id' => rand(0, 100000000), 'from' => $from[0]['id'], 'to' => $to[0]['id']];
                        }
                    }
                }
            }
        }

        return (object) $flow;
    }

    public function storeFromNodes($request){
        $this->meta = (object) [
            'centerX' => $request->centerX,
            'centerY' => $request->centerY,
            'scale' => $request->scale
        ];

        // Delete nodes that were not given
        $this->types()->whereNotIn('id',
            array_map(function($n){return $n['real_id'];},
                array_filter(
                    $request->nodes,
                    function($n){ return $n['type'] === 'Chat'; }
                )
            )
        )->delete();

        // Without start
        $loose = array_filter($request->nodes, function($node) use ($request){
            return $node['type'] === 'Chat' && !in_array($node['id'], array_map(function($node){ return $node['to']; }, $request->links));
        });

        if(count($loose) !== 1){
            return false;
        }
        $start = $loose[0];

        if($start['type'] === 'Chat'){
            $chat = Type::findOrCreate($start, $this, $request);
            $this->type_id = $chat->id;
            $this->save();
        }

        // Update coordinates for all
        foreach($this->types as $type){
            $node = $this->getNodeFromModel($type, $request);
            $type->x = $node['x'];
            $type->y = $node['y'];
            $type->save();
        }

        $this->refresh();

        return $this->toNodes();
    }

    private function getNodeFromModel($type, $request){
        return array_values(array_filter($request->nodes, function($n) use ($type){ return $n['type'] === 'Chat' && $n['real_id'] === $type->id;}))[0];
    }
}
