<?php

use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\Text;

return [

    Tab::make(__('Field', 'mosa-forms'),'tab_field')
        ->placement('left'),

    Text::make(__('Label', 'mosa-forms'), 'label')
        ->required()

];