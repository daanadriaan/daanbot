<?php
namespace App\Models\Steps;

use App\Models\Step;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Option extends Step
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('option', function (Builder $builder) {
            $builder->where('type', 'Option');
        });

        static::saved(function($model){
            $model->type = 'Option';
        });
    }

    public function getRedirectAttribute()
    {
        return $this->children()->inRandomOrder()->first();
    }
}
