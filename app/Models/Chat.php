<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class Chat extends Model
{
    public $appends = ['options'];

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function type(){
        return $this->belongsTo(Type::class);
    }

    public function getOptionsAttribute(){
        return optional($this->type)->options;
    }
}
