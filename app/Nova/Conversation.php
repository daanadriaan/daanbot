<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Conversation extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Conversation::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'created_at';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'token', 'created_at'
    ];

    public static function indexQuery(NovaRequest $request, $query): Builder {
        return $query->withCount('chats');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            DateTime::make('Created At'),
            Text::make('Token'),
            Number::make('Chats', 'chats_count')
                ->sortable()
                ->exceptOnForms(),
            HasMany::make('Chats', 'chats', Chat::class),
        ];
    }
}
