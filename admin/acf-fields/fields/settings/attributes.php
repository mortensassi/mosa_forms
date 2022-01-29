<?php

use WordPlate\Acf\Fields\Group;
use WordPlate\Acf\Fields\Repeater;
use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\Text;

return [
    Tab::make(__('Attributes', 'mosa-forms'),'tab_extensions_attributes')
        ->placement('left'),


    Group::make(__('Attributes','mosa-forms'),'attributes')
        ->layout('block')
        ->fields([

            Text::make(__('ID','mosa-forms'),'id')
                ->prepend('#')
                ->instructions(__('add an custom id to form field','mosa-forms')),

            Text::make(__('Classes','mosa-forms'),'class')
                ->prepend('')
                ->instructions(__('add custom classes to form field seperated by whitespace if more than one','mosa-forms')),

            Repeater::make(__('Custom data attributes','mosa-forms'),'custom')
                ->instructions(__('Add custom attributes to form field.', 'mosa-forms'))
                ->buttonLabel(__('add custom attribute','mosa-forms'))
                ->fields([
                    Text::make(__('Attribute', 'mosa-forms'), 'attribute')
                        ->required()
                        ->wrapper([
                            'width' => 33
                        ]),

                    Text::make(__('Value', 'mosa-forms'), 'value')
                        ->required()
                        ->wrapper([
                            'width' => 66
                        ]),

                ])
        ])

];