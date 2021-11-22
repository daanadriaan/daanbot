<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Step extends Model
{
    protected $visible = ['id', 'label', 'content', 'options'];
    protected $table = 'steps';

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(Step::class, 'redirects', 'from', 'to');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Step::class, 'redirects', 'from', 'to');
    }

    public function toNode(){
        $node = rand(0, 10000000);
        return [
            'id' => $node,
            'real_id' => $this->id,
            'label' => $this->label,
            'content' => $this->content,
            'type' => $this->type,
            'x' => $this->x,
            'y' => $this->y,
        ];
    }

    public static function findFromNodeOrCreate($node, $flow = null){
        if(!$step = self::find($node['real_id'])){
            $step = new Step;
        }
        if($flow){
            $step->flow_id = $flow->id;
        }
        $step->label = $node['label'];
        $step->type = $node['type'];
        $step->x = $node['x'];
        $step->y = $node['y'];
        $step->content = $node['content'] ?? null;
        $step->save();

        return $step;
    }
}
