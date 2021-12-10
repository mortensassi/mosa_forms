<?php

use WordPlate\Acf\Fields\FlexibleContent;
use WordPlate\Acf\Fields\Group;
use WordPlate\Acf\Fields\Image;
use WordPlate\Acf\Fields\Layout;
use WordPlate\Acf\Fields\PostObject;
use WordPlate\Acf\Fields\Repeater;
use WordPlate\Acf\Fields\Select;
use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\Textarea;
use WordPlate\Acf\Fields\TrueFalse;
use WordPlate\Acf\Fields\WysiwygEditor;
use WordPlate\Acf\Fields\Text;

return [
	'form' => [
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
															 Repeater::make(__('Groups', 'mosa'), 'groups')
																	 ->layout('block')
																	 ->buttonLabel(__('+ Add Checkbox Group', 'mosa'))
																	 ->fields([
																		 Text::make(__('Name', 'mosa'), 'name'),
																		 Textarea::make(
																			 __('Checkboxes', 'mosa'),
																			 'checkboxes'
																		 )
																				 ->instructions(
																					 __('One Checkbox per line', 'mosa')
																				 )
																	 ])
														 ]),
												   Layout::make(__('Input', 'mosa'), 'input')
														 ->fields([
															 Text::make(__('Label', 'mosa'), 'label')
																 ->wrapper([
																	 'width' => '50%'
																 ]),
															 Select::make(__('Type', 'mosa'), 'type')
																   ->choices([
																	   'text' => __('Text', 'mosa'),
																	   'email' => __('Email', 'mosa'),
																	   'tel' => __('Phone', 'mosa'),
																	   'file' => __('File', 'mosa')
																   ])
																   ->defaultValue('text')
																   ->stylisedUi()
																   ->wrapper([
																	   'width' => '25%'
																   ]),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => '25%'
																	  ])
														 ]),
												   Layout::make(__('Textarea', 'mosa'), 'textarea')
														 ->fields([
															 Text::make(__('Label', 'mosa'), 'label')
														 ]),
												   Layout::make(__('Select', 'mosa'), 'select')
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
															 Text::make(__('Title', 'mosa'), 'title'),
															 Repeater::make(__('Choices', 'mosa'), 'choices')
																	 ->buttonLabel(__('Add Choice', 'mosa'))
																	 ->fields([
																		 Text::make(__('Choice', 'mosa'), 'choice')
																	 ])
														 ]),
												   Layout::make(__('Checkbox', 'mosa'), 'checkbox')
														 ->fields([
															 Text::make(__('Label', 'mosa'), 'label')
																 ->wrapper([
																	 'width' => '50%'
																 ]),
															 TrueFalse::make(__('Required', 'mosa'), 'is_required')
																	  ->stylisedUi()
																	  ->wrapper([
																		  'width' => '50%'
																	  ]),
															 WysiwygEditor::make(__('Description', 'mosa'), 'description')
																		  ->delay()
																		  ->mediaUpload(0)
																		  ->tabs('visual')
																		  ->toolbar('mosa_forms_toolbar'),
														 ]),
												   Layout::make(__('Cards', 'mosa'), 'cards')
														 ->fields([
															 Text::make(__('Label', 'mosa'), 'label'),
															 Repeater::make(__('Choices', 'mosa'), 'choices')
																	 ->buttonLabel(__('Add Choice', 'mosa'))
																	 ->layout('block')
																	 ->fields([
																		 Text::make(__('Choice', 'mosa'), 'title'),
																		 WysiwygEditor::make(
																			 __('Description', 'mosa'),
																			 'description'
																		 )
																					  ->delay()
																					  ->mediaUpload(0)
																					  ->tabs('visual')
																					  ->toolbar('mosa_forms_toolbar'),
																		 Group::make(
																			 __('Pricing Table', 'mosa'),
																			 'pricing_table'
																		 )
																			  ->fields([
																				  Text::make(
																					  __('Headline', 'mosa'),
																					  'headline'
																				  ),
																				  Repeater::make(
																					  __('Pricing', 'mosa'),
																					  'pricing'
																				  )
																						  ->buttonLabel(
																							  __('Add Price', 'mosa')
																						  )
																						  ->fields([
																							  Text::make(
																								  __('Title', 'mosa'),
																								  'title'
																							  ),
																							  Repeater::make(
																								  __('Price', 'mosa'),
																								  'price'
																							  )
																									  ->fields([
																										  Text::make(
																											  __(
																												  'Audience',
																												  'mosa'
																											  ),
																											  'audience'
																										  ),
																										  Text::make(
																											  __(
																												  'Price',
																												  'mosa'
																											  ),
																											  'value'
																										  ),
																									  ])
																						  ])
																			  ]),
																	 ])
														 ])
											   ])
							]),
				]),
		Tab::make(__('Compliance', 'mosa')),
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
