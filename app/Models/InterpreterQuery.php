<?php
namespace App\Models;

use App\Models\Steps\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InterpreterQuery extends Model
{
    public function interpretable(){
        return $this->belongsTo(Interpretable::class);
    }
}
