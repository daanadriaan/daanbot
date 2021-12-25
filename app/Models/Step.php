<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Step extends Model
{
    protected $visible = ['id', 'label', 'content', 'options', 'redirect_flow_id', 'redirect'];
    protected $table = 'steps';

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(Step::class, 'redirects', 'from', 'to');
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Step::class, 'redirects', 'from', 'to');
    }

    public function redirect()
    {
        return $this->belongsTo(Flow::class, 'redirect_flow_id');
    }

    public function toNode(){
        $node = rand(0, 10000000);
        return [
            'id' => $node,
            'real_id' => $this->id,
            'label' => $this->label,
            'content' => $this->content,
            'type' => $this->type,
            'redirect_flow_id' => $this->redirect_flow_id,
            'redirect' => $this->redirect,
            'x' => $this->x,
            'y' => $this->y,
            'class' => 'blue',
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
        $step->redirect_flow_id = $node['redirect'] ? $node['redirect']['id'] : null;
        $step->content = $node['content'] ?? null;
        $step->save();

        return $step;
    }
}
