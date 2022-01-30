<?php

use WordPlate\Acf\ConditionalLogic;
use WordPlate\Acf\Fields\Group;
use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\Text;
use WordPlate\Acf\Fields\TrueFalse;

return [

    Tab::make(__('Validation', 'mosa-forms'),'tab_field_validation')
        ->placement('left'),

    Group::make(__('Validation','mosa-forms'),'validation')
        ->layout('block')
        ->fields(array_merge([

                TrueFalse::make(__('Required field','schrittweiter'),'is_required')
                    ->message(__('Make this field a required one','mosa-forms')),

                Text::make(__('Required message', 'mosa'), 'is_required_message')
                    ->required()
                    ->defaultValue(__('This field is required and can not left empty.','mosa-forms'))
                    ->conditionalLogic([
                        ConditionalLogic::if('is_required')->equals(1)
                    ]),
            ],$additonalValidation)
    )
];