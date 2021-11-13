<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public function type(){
        return $this->belongsToMany(Type::class)->withPivot('label');
    }
    public function redirect(){
        return $this->belongsTo(Type::class, 'redirect_to');
    }
}
