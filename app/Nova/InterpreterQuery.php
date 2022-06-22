<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;

class InterpreterQuery extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\InterpreterQuery::class;

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'query',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            BelongsTo::make('Interpreter', 'interpretable', Interpretable::class),
            Text::make('Query')
                ->sortable()
                ->rules('required', 'max:255'),

            Number::make('Percentage'),
            Number::make('Counter')->exceptOnForms()
        ];
    }
}
