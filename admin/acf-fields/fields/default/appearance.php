<?php

use WordPlate\Acf\ConditionalLogic;
use WordPlate\Acf\Fields\Group;
use WordPlate\Acf\Fields\Select;
use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\Text;
use WordPlate\Acf\Fields\TrueFalse;

return [

    Tab::make(__('Appearance', 'mosa-forms'),'tab_field_appearance')
        ->placement('left'),


    Group::make(__('Appearance','mosa-forms'),'appearance')
        ->layout('block')
        ->fields([

            Select::make(__('field size', 'mosa-forms'), 'size')
                ->defaultValue('full')
                ->required()
                ->choices([
                    'full'                  => __('full', 'mosa-forms'),
                    'half'                  => __('half', 'mosa-forms'),
                    'three_quarter'         => __('three-quarter', 'mosa-forms'),
                    'two_third'             => __('two-thirds', 'mosa-forms'),
                    'one_third'             => __('one-third', 'mosa-forms'),
                    'one_quarter'           => __('quarter', 'mosa-forms'),
                ]),

        ])
];