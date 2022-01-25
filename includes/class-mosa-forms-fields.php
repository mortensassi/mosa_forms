<?php

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
										  ->mediaUpload(0)
										  ->tabs('visual')
										  ->delay(true),
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
																				 ->buttonLabel(
																					 __('+ Add Checkbox', 'mosa')
																				 )
																				 ->fields([
																					 Text::make(
																						 __('Checkbox', 'mosa'),
																						 'checkbox'
																					 ),
																					 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																						 ->wrapper([
																							 'width' => 25
																						 ])
																						 ->required(),
																					 TrueFalse::make(__('Checked', 'mosa'), 'checked')
																					 ->wrapper([
																						 'width' => 15
																					 ])
																				 ])
																	 ])
														 ]),
												   Layout::make(__('Input', 'mosa'), 'input')
														 ->fields([
															 Text::make(__('Fieldname', 'mosa'), 'fieldname')
																 ->required(),
															 Text::make(__('Label', 'mosa'), 'label')
																 ->wrapper([
																	 'width' => (100/3 * 2)
																 ]),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => (100/3 * 1)
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
														 ]),
												   Layout::make(__('Price Range'), 'price_range')
														 ->fields([
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
																  ]),
															 Group::make(__('Infotext', 'mosa'), 'info')
															 ->fields([
																 Text::make(__('Label', 'mosa'), 'label'),
																 WysiwygEditor::make(__('Description', 'mosa'), 'description')
																 ->toolbar('basic')
																 ->mediaUpload(0)
																 ->tabs('visual')
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
																		  ->mediaUpload(0)
																		  ->tabs('visual')
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
																				 'width' => '50%'
																			 ]),
																		 Number::make(
																			 __('Maximum Input value', 'mosa'),
																			 'max_val'
																		 )
																			 ->wrapper([
																				 'width' => '50%'
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
								 ->mediaUpload(0)
								 ->tabs('visual')
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
							  ->mediaUpload(0)
							  ->tabs('visual')
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
