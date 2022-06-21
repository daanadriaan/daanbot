<?php
namespace App\Models\Steps;

use App\Models\Step;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Response extends Step
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('response', function (Builder $builder) {
            $builder->where('type', 'Response');
        });

        static::saved(function($model){
            $model->type = 'Response';
            $model->content = $model->content ?? 'Placeholder';
        });
    }

    public function options(): BelongsToMany
    {
        return $this->belongsToMany(Step::class, 'redirects', 'from', 'to')
            ->where('type', 'Option')
            ->orderBy('x');
    }
}
