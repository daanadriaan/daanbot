<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
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

        // Hier moet de nieuwe node komen..
        $option->redirect_to = 1;

        $option->save();

        $option->types()->detach($chat);
        $option->types()->attach($chat, ['label' => $node['label']]);

        return $option;
    }
}
