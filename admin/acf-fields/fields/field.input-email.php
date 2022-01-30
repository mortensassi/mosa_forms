<?php

/*
 * Title: Input - Email
 * Category: Basic
 */


use Schrittweiter\Acf\Fields\Columns;
use WordPlate\Acf\ConditionalLogic;
use WordPlate\Acf\Fields\Number;
use WordPlate\Acf\Fields\Text;

$additonalValidation = [

    Text::make(__('Invalid E-Mail message', 'mosa'), 'invalid_email_message')
        ->required()
        ->instructions(__('The error message that is displayed to the user as he enters an incorrect email address.','mosa-forms'))
        ->defaultValue(__('The entered e-mail address is not valid.','mosa-forms')),
];

return [

    Text::make(__('Placeholder', 'mosa-forms'), 'placeholder')
        ->instructions(__('Set an individual placeholder for the input field - if not set the label will be used as placeholder','mosa-forms'))

];