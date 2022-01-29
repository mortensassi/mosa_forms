<?php

use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\TrueFalse;

return [

    Tab::make(__('Validation', 'mosa-forms'),'tab_field_validation')
        ->placement('left'),

    TrueFalse::make(__('Required field','schrittweiter'),'is_required')
        ->message(__('Make this field a required one','mosa-forms'))

];