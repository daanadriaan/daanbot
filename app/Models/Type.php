<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    public function chats(){
        return $this->hasMany(Chat::class, 'type');
    }
    public function options(){
        return $this->belongsToMany(Option::class)->withPivot('label');
    }
}
