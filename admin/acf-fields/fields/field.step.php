<?php

/*
 * Title: Step
 * Category: Layout
 */


use WordPlate\Acf\Fields\Text;

return [

    Text::make(__('prev button text', 'mosa-forms'), 'step_prev')
        ->defaultValue(__('Previous step', 'mosa-forms'))
        ->required()
        ->wrapper([
            'width' => 50
        ]),

    Text::make(__('next button text', 'mosa-forms'), 'step_next')
        ->defaultValue(__('Next step', 'mosa-forms'))
        ->required()
        ->wrapper([
            'width' => 50
        ])

];