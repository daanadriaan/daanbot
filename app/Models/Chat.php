<?php
namespace App\Models;

use App\Models\Steps\Response;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    public $appends = ['options'];
    protected $visible = ['content', 'options'];

    public function conversation(){
        return $this->belongsTo(Conversation::class);
    }

    public function response(){
        return $this->belongsTo(Response::class, 'type_id');
    }

    public function getOptionsAttribute(){
        return optional($this->response)->options;
    }
    public function getContentAttribute(){
        return optional($this->response)->content;
    }
}
