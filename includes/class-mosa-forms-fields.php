<?php

use Schrittweiter\Acf\Fields\Table;
use WordPlate\Acf\ConditionalLogic;
use WordPlate\Acf\Fields\FlexibleContent;
use WordPlate\Acf\Fields\Group;
use WordPlate\Acf\Fields\Image;
use WordPlate\Acf\Fields\Layout;
use WordPlate\Acf\Fields\Number;
use WordPlate\Acf\Fields\PostObject;
use WordPlate\Acf\Fields\Repeater;
use WordPlate\Acf\Fields\Select;
use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\Textarea;
use WordPlate\Acf\Fields\TrueFalse;
use WordPlate\Acf\Fields\WysiwygEditor;
use WordPlate\Acf\Fields\Text;

return [
	'form'     => [
		PostObject::make(__('Select a form', 'mosa'), 'form')
				  ->returnFormat('id')
				  ->postTypes(['mosa_form'])
	],
	'settings' => [
		Text::make(__('Title', 'mosa'), 'title'),
		Tab::make(__('Form', 'mosa')),
		Repeater::make(__('Steps', 'mosa'), 'steps')
				->layout('block')
				->buttonLabel(__('+ Add Step', 'mosa'))
				->fields([
					Group::make(__('Header', 'mosa'), 'header')
						 ->layout('block')
						 ->fields([
							 Text::make(__('Title', 'mosa'), 'title'),
							 WysiwygEditor::make(__('Content', 'mosa'), 'editor_content')
										  ->mediaUpload(false)
										  ->delay(),
							 Image::make(__('Image', 'mosa'), 'image')
						 ]),
					Repeater::make(__('Groups', 'mosa'), 'groups')
							->layout('block')
							->buttonLabel(__('+ Add Group', 'mosa'))
							->fields([
								Text::make(__('Title', 'mosa'), 'title'),
								FlexibleContent::make(__('Fields', 'mosa'), 'fields')
											   ->buttonLabel(__('+ Add Field', 'mosa'))
											   ->layouts([
												   Layout::make(__('Grouped Checkboxes', 'mosa'), 'grouped_checkboxes')
														 ->fields([
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																 ->wrapper([
																	 'width' => 75
																 ])
																 ->required(),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => 25
																	  ]),
															 Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_required')->equals(1)
																 ])
																 ->required(),
															 Repeater::make(__('Groups', 'mosa'), 'groups')
																	 ->layout('block')
																	 ->buttonLabel(__('+ Add Checkbox Group', 'mosa'))
																	 ->fields([
																		 Text::make(__('Name', 'mosa'), 'name'),
																		 Repeater::make(
																			 __('Checkboxes', 'mosa'),
																			 'checkboxes'
																		 )
																			 	->layout('block')
																				 ->buttonLabel(
																					 __('+ Add Checkbox', 'mosa')
																				 )
																				 ->fields([
																					 Text::make(
																						 __('Checkbox', 'mosa'),
																						 'checkbox'
																					 )
																						 ->wrapper([
																							 'width' => 60
																						 ]),
																					 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																						 ->wrapper([
																							 'width' => 25
																						 ])
																						 ->required(),
																					 TrueFalse::make(__('Checked', 'mosa'), 'checked')
																					 ->wrapper([
																						 'width' => 15
																					 ]),

																					 Table::make(__('Tabellen Popup','schrittweiter'),'table_popup')
																						 ->wrapper([
																							 'width' => 100
																						 ]),
																				 ])
																	 ])
														 ]),
												   Layout::make(__('Input', 'mosa'), 'input')
														 ->fields([
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																 ->required(),
															 Text::make(__('Label', 'mosa'), 'label')
																 ->wrapper([
																	 'width' => 60
																 ]),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->wrapper([
																		  'width' => 20
																	  ]),
															 TrueFalse::make(__('In Duplicate', 'mosa'), 'in_duplicate')
																	  ->wrapper([
																		  'width' => 20
																	  ]),
															 Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_required')->equals(1)
																 ])
																 ->required(),
															 Select::make(__('Type', 'mosa'), 'type')
																   ->choices([
																	   'text'  => __('Text', 'mosa'),
																	   'number' => __('Number', 'mosa'),
																	   'email' => __('Email', 'mosa'),
																	   'tel'   => __('Phone', 'mosa'),
                                                                       'date'   => __('Date', 'mosa')
																   ])
																   ->defaultValue('text')
																   ->stylisedUi()
																   ->wrapper([
																	   'width' => 50
																   ]),
															 Select::make(__('Size', 'mosa'), 'size')
																   ->choices([
																	   'full'  => __('Full', 'mosa'),
																	   'one_third' => __('One-third', 'mosa'),
																	   'two_third' => __('Two-third', 'mosa'),
																   ])
																   ->defaultValue('full')
																   ->stylisedUi()
																   ->wrapper([
																	   'width' => 50
																   ]),
															 Text::make(__('Platzhalter', 'mosa'), 'placeholder')
														 ]),
												   Layout::make(__('Price Range'), 'price_range')
														 ->fields([
															 Text::make(__('Label', 'mosa'), 'label')
																 ->wrapper([
																	 'width' => 60
																 ]),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->wrapper([
																		  'width' => 20
																	  ]),
															 TrueFalse::make(__('In Duplicate', 'mosa'), 'in_duplicate')
																	  ->wrapper([
																		  'width' => 20
																	  ]),
															 Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_required')->equals(1)
																 ])
																 ->required(),
															 TrueFalse::make(__('Toggle Min value', 'mosa'), 'toggle_min')
																->stylisedUi(),
															 Group::make(__('Min Value', 'mosa'), 'min')
																  ->layout('block')
																  ->fields([
																	  Text::make(__('Fieldname', 'mosa'), 'fieldname')
																		  ->required(),
																	  Number::make(__('Value', 'mosa'), 'val')
																  ])
																	->conditionalLogic([
																		ConditionalLogic::if('toggle_min')->equals(1)
																	]),
															 Group::make(__('Max Value', 'mosa'), 'max')
																  ->layout('block')
																  ->fields([
																	  Text::make(__('Fieldname', 'mosa'), 'fieldname')
																		  ->required(),
																	  Number::make(__('Value', 'mosa'), 'val')
																		  ->wrapper([
																			  'width' => 50
																		  ]),
																	  Number::make(__('Max Value', 'mosa'), 'max_val')
																		  ->wrapper([
																			  'width' => 50
																		  ])
																  ]),
															 Group::make(__('Infotext', 'mosa'), 'info')
															 ->fields([
																 Text::make(__('Label', 'mosa'), 'label'),
																 WysiwygEditor::make(__('Description', 'mosa'), 'description')
																 ->toolbar('basic')
																 ->mediaUpload(false)
															 ])
														 ]),
												   Layout::make(__('Button Group'), 'button_group')
														 ->fields([
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																 ->required(),
															 Text::make(__('Label', 'mosa'), 'label')
																 ->wrapper([
																	 'width' => '75%'
																 ]),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => '25%'
																	  ]),
															 Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_required')->equals(1)
																 ])
																 ->required(),
															 Repeater::make(__('Buttons', 'mosa'), 'buttons')
																	 ->layout('block')
																	 ->buttonLabel(__('+ Add Button', 'mosa'))
																	 ->fields([
																		 Text::make(__('Label', 'mosa'), 'label')
																			 ->wrapper([
																				 'width' => '50%'
																			 ]),
																		 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																			 ->wrapper([
																				 'width' => '20%'
																			 ])
																			 ->required(),
																		 TrueFalse::make(
																			 __('Tooltip', 'mosa'),
																			 'has_info'
																		 )
																				  ->wrapper([
																					  'width' => '15%'
																				  ]),
																		 TrueFalse::make(__('Selected', 'mosa'), 'selected')
																			 ->wrapper([
																				 'width' => '10%'
																			 ]),
																		 Text::make(__('Info text', 'mosa'), 'info')
																			 ->conditionalLogic([
																				 ConditionalLogic::if(
																					 'has_info'
																				 )->equals(1)
																			 ])
																	 ])
														 ]),
												   Layout::make(__('Textarea', 'mosa'), 'textarea')
														 ->fields([
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
															 	->required(),
															 Text::make(__('Label', 'mosa'), 'label')
														 ]),
												   Layout::make(__('Select', 'mosa'), 'select')
														 ->fields([
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
															 	->required(),
															 Text::make(__('Label', 'mosa'), 'label'),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => '25%'
																	  ]),
															 TrueFalse::make(__('In Duplicate', 'mosa'), 'in_duplicate')
																	  ->wrapper([
																		  'width' => 20
																	  ]),
															 Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_required')->equals(1)
																 ])
																 ->required(),
															 Repeater::make(__('Choices', 'mosa'), 'choices')
																	 ->buttonLabel(__('+ Add Choice', 'mosa'))
																	 ->fields([
																		 Text::make(__('Choice', 'mosa'), 'choice')
																			 ->wrapper([
																				 'width' => '70%'
																			 ]),
																		 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																			 ->required()
																			 ->wrapper([
																				 'width' => '15%'
																			 ]),
																		 TrueFalse::make(__('Selected', 'mosa'), 'selected')
																			 ->wrapper([
																				 'width' => '15%'
																			 ]),
																	 ])
														 ]),
												   Layout::make(__('Multiselect', 'mosa'), 'multiselect')
														 ->fields([
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
                                                                 ->wrapper([
                                                                     'width' => 70
                                                                 ])
															 	->required(),
															 Text::make(__('Linked field', 'mosa'), 'link')
																 ->wrapper([
																	 'width' => 30
																 ]),
															 Text::make(__('Label', 'mosa'), 'label')
																 ->wrapper([
																	 'width' => '75%'
																 ]),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => '25%'
																	  ]),
															 Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_required')->equals(1)
																 ])
																 ->required(),
															 Text::make(__('Title', 'mosa'), 'title'),
															 Repeater::make(__('Choices', 'mosa'), 'choices')
																	 ->buttonLabel(__('+ Add Choice', 'mosa'))
																     ->layout('block')
																	 ->fields([
																		 Text::make(__('Group', 'mosa'), 'group'),
																		 Repeater::make(
																			 __('Choices', 'mosa'),
																			 'choices'
																		 )
																				 ->buttonLabel(
																					 __('+ Add Choice', 'mosa')
																				 )
																				 ->fields([
																					 Text::make(
																						 __('Choice', 'mosa'),
																						 'choice'
																					 ),
																					 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																						 ->wrapper([
																							 'width' => 15
																						 ])
																						 ->required(),
																					 Text::make(__('Region ID', 'mosa'), 'region')
																						 ->wrapper([
																							 'width' => 15
																						 ]),
																					 TrueFalse::make(__('Selected', 'mosa'), 'selected')
																					 	->wrapper([
																							 'width' => 15
																						]),
																				 ])
																	 ])
														 ]),
												   Layout::make(__('Checkbox', 'mosa'), 'checkbox')
														 ->fields([
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
															 	->required(),
															 Text::make(__('Label', 'mosa'), 'label')
																 ->wrapper([
																	 'width' => '50%'
																 ]),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => '50%'
																	  ]),
															 Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_required')->equals(1)
																 ])
																 ->required(),
															 WysiwygEditor::make(
																 __('Description', 'mosa'),
																 'description'
															 )
																		  ->delay()
																		  ->mediaUpload(false)
																		  ->toolbar('mosa_forms_toolbar'),

														 ]),
												   Layout::make(__('Counter', 'mosa'), 'counter')
														 ->fields([
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
															 	->required(),
															 TrueFalse::make(
																 __('Required', 'mosa'),
																 'is_required'
															 )
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => '50%'
																	  ]),
															 Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
																 ->wrapper([
																	 'width' => '50%'
																 ])
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_required')->equals(1)
																 ])
																 ->required(),
															 Repeater::make(__('Counter', 'mosa'), 'inputs')
																 	->layout('block')
																	 ->buttonLabel(__('+ Add Counter', 'mosa'))
																	 ->fields([
																		 Text::make(__('Fieldname', 'mosa'), 'fieldname')
															 				->required(),
																		 Text::make(__('Label', 'mosa'), 'label')
																			 ->wrapper([
																				 'width' => 33
																			 ]),
																		 Number::make(
																			 __('Minimum Input value', 'mosa'),
																			 'min_val'
																		 )
																			   ->wrapper([
																				   'width' => 33
																			   ]),
																		 Number::make(
																			 __('Maximum Input value', 'mosa'),
																			 'max_val'
																		 )
																			 ->wrapper([
																				 'width' => 33
																			 ]),
																	 ])
														 ]),
												   Layout::make(__('Choices', 'mosa'), 'choices')
														 ->fields([
															 Text::make(__('Label', 'mosa'), 'label')
																 ->wrapper([
																	 'width' => '60%'
																 ]),
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
															 	->required(),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => '25%'
																	  ]),
															 Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_required')->equals(1)
																 ])
																 ->required(),
															 Repeater::make(__('Buttons', 'mosa'), 'buttons')
																	 ->buttonLabel(__('+ Add Button', 'mosa'))
																	 ->fields([
																		 Text::make(__('Button ', 'mosa'), 'text'),
																		 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																			 ->required(),
																		 TrueFalse::make(__('Selected', 'mosa'), 'selected')
																				  ->wrapper([
																					  'width' => '15%'
																				  ]),
																		 TrueFalse::make(
																			 __('Tooltip', 'mosa'),
																			 'has_info'
																		 )
																			 ->wrapper([
																				 'width' => '15%'
																			 ]),
																		 Text::make(__('Info text', 'mosa'), 'info')
																			 ->conditionalLogic([
																				 ConditionalLogic::if(
																					 'has_info'
																				 )->equals(1)
																			 ])
																	 ]),

															 TrueFalse::make(__('KDU Check', 'mosa'), 'is_kdu_check')
																 ->stylisedUi()
																 ->wrapper([
																	 'width' => '50%'
																 ]),

															 Group::make(__('KDU Raten','mosa'), 'kdu_rates')
																 ->conditionalLogic([
																	 ConditionalLogic::if('is_kdu_check')->equals(1)
																 ])
																 ->fields([
																	 Number::make(__('Max Miete Warm', 'mosa'), 'persons_one')
																		 ->prepend('1 Person')
																		 ->append('€')
																		 ->required(),

																	 Number::make(__('Max Miete Warm', 'mosa'), 'persons_two')
																		 ->prepend('2 Personen')
																		 ->append('€')
																		 ->required(),

																	 Number::make(__('Max Miete Warm', 'mosa'), 'persons_three')
																		 ->prepend('3 Personen')
																		 ->append('€')
																		 ->required(),

																	 Number::make(__('Max Miete Warm', 'mosa'), 'persons_four')
																		 ->prepend('4 Personen')
																		 ->append('€')
																		 ->required(),

																	 Number::make(__('Max Miete Warm', 'mosa'), 'persons_five')
																		 ->prepend('5 Personen')
																		 ->append('€')
																		 ->required(),

																	 Number::make(__('Max Miete Warm', 'mosa'), 'persons_additional')
																		 ->prepend('jede weitere Person')
																		 ->append('€')
																		 ->required(),

																	 WysiwygEditor::make(__('Fehlermeldung', 'mosa'), 'error_message')
																		 ->mediaUpload(false)
																		 ->required(),
																 ])
														 ]),
												   Layout::make(__('Country Select', 'mosa'), 'countries')
												   	->fields([
														Text::make(__('Fieldname', 'mosa'), 'fieldname')
															 	->required(),
														Text::make(__('Label', 'mosa'), 'label')
															->wrapper([
																'width' => '75%'
															]),
														TrueFalse::make(__('Required', 'mosa'), 'is_required')
																 ->stylisedUi()
																 ->wrapper([
																	 'width' => '25%'
																 ]),
														Text::make(__('Fehlermeldung', 'mosa'), 'error_message')
															->conditionalLogic([
																ConditionalLogic::if('is_required')->equals(1)
															])
															->required(),
													]),
												   Layout::make(__('Duplicate fields', 'mosa'), 'duplicate')
												   	->fields([
														   TrueFalse::make(__('Duplicate fields from this group', 'mosa'), 'duplicate_fields')
															   ->wrapper([
																   'width' => '10%'
															   ]),
														Text::make(__('Text label', 'mosa'), 'label')
															->wrapper([
																'width' => '40%'
															]),
														Text::make(__('Text label remove', 'mosa'), 'label_remove')
															->wrapper([
																'width' => '40%'
															]),
                                                        Number::make(__('Max count', 'mosa'), 'max_count')
															->wrapper([
																'width' => '10%'
															])
                                                            ->required()
													])
											   ])
							]),
				]),
		Tab::make(__('Compliance', 'mosa')),
		Text::make(__('Headline', 'mosa'), 'compliance_headline'),
		Repeater::make(__('User Opt Check', 'mosa'), 'compliance_opt_check')
				->buttonLabel(__('Add Check', 'mosa'))
				->layout('block')
				->fields([
					TrueFalse::make(__('Required', 'mosa'), 'is_required')
							 ->stylisedUi()
							 ->wrapper([
								 'width' => '25%'
							 ]),
					WysiwygEditor::make(__('Opt-In Text', 'mosa'), 'text')
								 ->delay()
								 ->mediaUpload(false)
								 ->toolbar('mosa_forms_toolbar')
								 ->wrapper([
									 'width' => '75%'
								 ]),
				]),
		Repeater::make(__('Additional Info', 'mosa'), 'compliance_additional_info')
			->buttonLabel(__('Add Info', 'mosa'))
			->layout('block')
			->fields([
				Textarea::make(__('Info text', 'mosa'), 'text')
				->newLines('br')
			]),
		Tab::make(__('Mail', 'mosa')),
		Group::make(__('Mail Settings', 'mosa'), 'mail_settings')
			 ->layout('block')
			 ->fields([
				 Text::make(__('Subject', 'mosa'), 'subject'),
				 Text::make(__('Receiver', 'mosa'), 'recipient'),
				 WysiwygEditor::make(__('Success Mail', 'mosa'), 'body')
							  ->delay()
							  ->mediaUpload(false)
							  ->toolbar('mosa_forms_toolbar')
			 ]),
		Tab::make(__('Messages', 'mosa')),
		Group::make(__('Form Messages', 'mosa'), 'form_messages')
			 ->layout('block')
			 ->fields([
				 Text::make(__('Success', 'mosa'), 'success'),
				 Text::make(__('Warning', 'mosa'), 'warning'),
				 Text::make(__('Error', 'mosa'), 'error'),
			 ])
	]
];
