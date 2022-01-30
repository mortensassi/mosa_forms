<?php

/*
 * Title: Input - Text
 * Category: Basic
 */


use Schrittweiter\Acf\Fields\Columns;
use WordPlate\Acf\ConditionalLogic;
use WordPlate\Acf\Fields\Number;
use WordPlate\Acf\Fields\Text;

$additonalValidation = [

    Columns::make('', 'column_validation_min_input_length')
        ->columns('6/12')
        ->border(['column','fields']),

    Number::make(__('max input length', 'mosa-forms'), 'min_input_length')
        ->min(1)
        ->instructions(__('Set the minimum character length for the input field - Leave empty to skip validation','mosa-forms')),

    Text::make(__('Required message', 'mosa'), 'min_input_length_message')
        ->required()
        ->instructions(__('The message that will be displayed to the user if he has not entered enough characters. The placeholder __PLACEHOLDER_MIN__ can be used to output the number of characters.','mosa-forms'))
        ->defaultValue(__('The field must contain at least __PLACEHOLDER_MIN__ characters.','mosa-forms'))
        ->conditionalLogic([
            ConditionalLogic::if('min_input_length')->notEmpty()
        ]),

    Columns::make('', 'column_validation_max_input_length')
        ->columns('6/12')
        ->border(['column','fields']),

    Number::make(__('max input length', 'mosa-forms'), 'max_input_length')
        ->instructions(__('Set the maximum character length for the input field - Leave empty to skip validation')),

    Text::make(__('Required message', 'mosa'), 'max_input_length_message')
        ->required()
        ->instructions(__('The message that will be displayed to the user if he has entered more than the allowed amount of characters. The placeholder __PLACEHOLDER_MAX__ can be used to output the number of characters.','mosa-forms'))
        ->defaultValue(__('The field can be a maximum of __PLACEHOLDER_MAX__ characters long.','mosa-forms'))
        ->conditionalLogic([
            ConditionalLogic::if('max_input_length')->notEmpty()
        ]),

    Columns::make('', 'column_validation_min_input_length_end')
        ->endpoint()
];

return [

    Text::make(__('Placeholder', 'mosa-forms'), 'placeholder')
        ->instructions(__('Set an individual placeholder for the input field - if not set the label will be used as placeholder','mosa-forms'))

];