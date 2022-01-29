<?php

use WordPlate\Acf\Fields\Group;
use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\Text;
use WordPlate\Acf\Fields\TrueFalse;

return [
    Tab::make(__('Settings', 'mosa-forms'),'tab_extensions_settings')
        ->placement('left'),


    Group::make(__('Settings','mosa-forms'),'settings')
        ->layout('block')
        ->fields([

            Text::make(__('field name','mosa-forms'),'fieldname')
                ->instructions(__('add a custom field name','mosa-forms'))

        ])

];