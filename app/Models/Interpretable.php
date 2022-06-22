<?php
namespace App\Models;

use App\Models\Steps\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Interpretable extends Model
{
    public function queries(){
        return $this->hasMany(InterpreterQuery::class);
    }
    public function flow(){
        return $this->belongsTo(Flow::class);
    }
}
