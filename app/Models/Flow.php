<?php
namespace App\Models;

use App\Models\Steps\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Flow extends Model
{
    protected $casts = [
        'meta' => 'object',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($flow) {
            // Same code here
            $flow->meta = ["scale" => 1, "centerX" => 437, "centerY" => 288];
        });
    }

    public function steps(){
        return $this->hasMany(Step::class);
    }

    public function interpretables(){
        return $this->hasMany(Interpretable::class);
    }

    public function start(){
        return $this->belongsTo(Response::class, 'type_id');
    }

    public function redirectedFrom(){
        return $this->hasMany(Step::class, 'redirect_flow_id');
    }

    public function toNodes(){
        $flow =  [
            'centerX' =>  optional($this->meta)->centerX ?? 300,
            'centerY' => optional($this->meta)->centerY ?? 300,
            'scale' => optional($this->meta)->scale ?? 0,
            'nodes' => [],
            'links' => []
        ];

        // Create nodes
        foreach($this->steps as $step){
            $flow['nodes'][] = $step->toNode();
        }
        // Create steps
        foreach($this->steps as $step){
            $from = $this->getNodeFromNodesByModel($step, $flow['nodes']);
            foreach($step->children as $child){
                $to = $this->getNodeFromNodesByModel($child, $flow['nodes']);

                $flow['links'][] = [
                    'id' => rand(0, 100000000),
                    'from' => $from['id'],
                    'to' => $to['id'],
                ];
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

        $this->deleteOldSteps($request);

        if(!$this->determineStart($request)){
            // TODO: Throw error;
            return false;
        }

        $this->syncSteps($request);

        $this->refresh();

        return $this->toNodes();
    }

    private function getNodeFromNodesByModel($step, $nodes){
        $matches = array_values(array_filter($nodes, function($f) use ($step){ return $f['real_id'] === $step->id;}));
        return isset($matches[0]) ? $matches[0] : null;
    }

    private function deleteOldSteps($request){
        // Delete nodes that were not given
        $steps = $this->steps()->whereNotIn('id',
            array_map(function($n){ return $n['real_id'] ?? ''; },
                $request->nodes
            )
        )->get();

        foreach($steps as $step){
            $step->parents()->detach();
            $step->children()->detach();
            $step->delete();
        }
    }

    private function determineStart($request){

        $loose = array_filter($request->nodes, function($node) use ($request){
            return $node['type'] === 'Response' && !in_array($node['id'], array_map(function($node){ return $node['to']; }, $request->links));
        });

        if(count($loose) !== 1){
            return false;
        }
        $start = $loose[0];

        $chat = Step::findFromNodeOrCreate($start, $this);
        $flow = $this;
        $flow->type_id = $chat->id;
        $flow->save();

        return true;
    }

    private function syncSteps($request){
        foreach($request->nodes as $node){
            $step = Step::findFromNodeOrCreate($node, $this);

            $step->parents()->detach();
            $step->children()->detach();
        }

        foreach($request->links as $link){
            DB::table('redirects')->insert([
                'from' => $this->nodeIdToRealId($link['from'], $request->nodes),
                'to' =>  $this->nodeIdToRealId($link['to'], $request->nodes),
            ]);
        }

        return true;
    }

    private function nodeIdToRealId($id, $nodes){
        $match = array_values(array_filter($nodes, function($node) use ($id){
                return $node['id'] === $id;
            }))[0];
        return $match['real_id'];
    }
}
